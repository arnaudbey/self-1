<?php

namespace Innova\SelfBundle\Repository;

use Doctrine\ORM\EntityRepository;

class TraceRepository extends EntityRepository
{
    public function getByUserAndSession($userId, $sessionId)
    {
        $dql = "SELECT t FROM Innova\SelfBundle\Entity\Trace t
        WHERE t.user = :userId
        AND t.session = :sessionId";

        $query = $this->_em->createQuery($dql)
                ->setParameter('userId', $userId)
                ->setParameter('sessionId', $sessionId);

        return $query->getResult();
    }

    public function getBySessionAndQuestionnaire($sessionId, $questionnaireId)
    {
        $dql = "SELECT t FROM Innova\SelfBundle\Entity\Trace t
        WHERE t.questionnaire = :questionnaireId
        AND t.session = :sessionId";

        $query = $this->_em->createQuery($dql)
                ->setParameter('questionnaireId', $questionnaireId)
                ->setParameter('sessionId', $sessionId);

        return $query->getResult();
    }

    public function getByUserAndSessionAndQuestionnaire($userId, $sessionId, $questionnaireId)
    {
        $dql = "SELECT t FROM Innova\SelfBundle\Entity\Trace t
        WHERE t.user = :userId
        AND t.session = :sessionId
        AND t.questionnaire = :questionnaireId";

        $query = $this->_em->createQuery($dql)
                ->setParameter('userId', $userId)
                ->setParameter('sessionId', $sessionId)
                ->setParameter('questionnaireId', $questionnaireId);

        return $query->getResult();
    }

    public function getFirstForSecondStep($session, $user)
    {
        $dql = "SELECT t FROM Innova\SelfBundle\Entity\Trace t
        LEFT JOIN t.component c
        LEFT JOIN c.componentType ct
        WHERE t.user = :user
        AND t.session = :session
        AND ct.name != 'minitest'";

        $query = $this->_em->createQuery($dql)
                ->setMaxResults(1)
                ->setParameter('user', $user)
                ->setParameter('session', $session);

        return $query->getOneOrNullResult();
    }

    public function getLastBySession($session)
    {
        $dql = "SELECT t FROM Innova\SelfBundle\Entity\Trace t
        WHERE t.session = :session
        order by t.id DESC";

        $query = $this->_em->createQuery($dql)
                ->setMaxResults(1)
                ->setParameter('session', $session);

        return $query->getOneOrNullResult();
    }

    public function getByUserBySessionByComponent($user, $session, $component)
    {
        $dql = "SELECT t FROM Innova\SelfBundle\Entity\Trace t
        LEFT JOIN t.session s
        LEFT JOIN t.questionnaire q
        WHERE t.user = :user
        AND t.component = :component
        AND s = :session
        AND (
            EXISTS (
            SELECT oqc FROM Innova\SelfBundle\Entity\PhasedTest\OrderQuestionnaireComponent oqc
            LEFT JOIN oqc.component c
            WHERE c.test = s.test
            AND oqc.questionnaire = q
            AND oqc.ignoreInScoring = 0)
            OR
            EXISTS (
            SELECT oqt FROM Innova\SelfBundle\Entity\OrderQuestionnaireTest oqt
            WHERE oqt.questionnaire = q
            AND oqt.test = s.test)
        )";

        $query = $this->_em->createQuery($dql)
                ->setParameter('user', $user)
                ->setParameter('session', $session)
                ->setParameter('component', $component);

        return $query->getResult();
    }

    public function getByUserBySessionBySkill($user, $session, $skill)
    {
        $dql = "SELECT t FROM Innova\SelfBundle\Entity\Trace t
        LEFT JOIN t.session s
        LEFT JOIN t.questionnaire q
        WHERE t.user = :user
        AND s = :session
        AND q.skill = :skill
        AND (
            EXISTS (
            SELECT oqc FROM Innova\SelfBundle\Entity\PhasedTest\OrderQuestionnaireComponent oqc
            LEFT JOIN oqc.component c
            WHERE c.test = s.test
            AND oqc.questionnaire = q
            AND oqc.ignoreInScoring = 0)
            OR
            EXISTS (
            SELECT oqt FROM Innova\SelfBundle\Entity\OrderQuestionnaireTest oqt
            WHERE oqt.questionnaire = q
            AND oqt.test = s.test)
        )";

        $query = $this->_em->createQuery($dql)
                ->setParameter('user', $user)
                ->setParameter('session', $session)
                ->setParameter('skill', $skill);

        return $query->getResult();
    }

    public function getByUserBySession($user, $session)
    {
        $dql = "SELECT t FROM Innova\SelfBundle\Entity\Trace t
        LEFT JOIN t.session s
        LEFT JOIN t.questionnaire q
        WHERE t.user = :user
        AND s = :session
        AND (
            EXISTS (
            SELECT oqc FROM Innova\SelfBundle\Entity\PhasedTest\OrderQuestionnaireComponent oqc
            LEFT JOIN oqc.component c
            WHERE c.test = s.test
            AND oqc.questionnaire = q
            AND oqc.ignoreInScoring = 0)
            OR
            EXISTS (
            SELECT oqt FROM Innova\SelfBundle\Entity\OrderQuestionnaireTest oqt
            WHERE oqt.questionnaire = q
            AND oqt.test = s.test)
        )";

        $query = $this->_em->createQuery($dql)
                ->setParameter('user', $user)
                ->setParameter('session', $session);

        return $query->getResult();
    }

    /**
     * countTraceByUserByTestByQuestionnaire Count Trace By user/test/questionnaire.
     *
     * @param id $test
     * @param id $questionnaire
     * @param id $user
     *
     * @return int number of traces for the test and the questionnaire and the user
     */
    public function findByUserByTestByQuestionnaire($test, $questionnaire, $user, $component, $session)
    {
        $dql = "SELECT t FROM Innova\SelfBundle\Entity\Trace t
        WHERE t.user = :user
        AND t.test = :test
        AND t.session = :session
        AND t.questionnaire = :questionnaire";

        if ($component) {
            $dql .= ' AND t.component = :component';
        }

        $query = $this->_em->createQuery($dql)
                ->setParameter('test', $test)
                ->setParameter('questionnaire', $questionnaire)
                ->setParameter('user', $user)
                ->setParameter('session', $session);

        if ($component) {
            $query->setParameter('component', $component);
        }

        return $query->getResult();
    }

    public function findDuplicate()
    {
        $dql = "SELECT t FROM Innova\SelfBundle\Entity\Trace t
        WHERE EXISTS (
            SELECT t FROM Innova\SelfBundle\Entity\Trace td
            WHERE td.id < t.id
            AND td.user = t.user
            AND td.test = t.test
            AND td.questionnaire = t.questionnaire
            AND ((td.session IS NULL AND t.session IS NULL) OR (td.session = t.session))
        )";

        $query = $this->_em->createQuery($dql);

        return $query->getResult();
    }
}
