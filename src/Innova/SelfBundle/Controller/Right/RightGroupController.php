<?php

namespace Innova\SelfBundle\Controller\Right;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Innova\SelfBundle\Entity\Right\RightGroup;
use Innova\SelfBundle\Entity\User;

/**
 * RightGroup controller.
 *
 * @Route("right-user-group-management")
 * @ParamConverter("rightGroup", isOptional="true", class="InnovaSelfBundle:Right\RightGroup", options={"id" = "rightGroupId"})
 * @ParamConverter("user", isOptional="true", class="InnovaSelfBundle:User", options={"id" = "userId"})
 */
class RightGroupController extends Controller
{
    /**
     *
     * @Route("user/{userId}/group/{rightGroupId}/toggle", name="editor_group_rights_toggle")
     * @Method("GET")
     * @Template("InnovaSelfBundle:Features:Group/rights.html.twig")
     */
    public function toggleAllForGroupAction(User $user, RightGroup $rightGroup)
    {
        $currentUser = $this->get('security.context')->getToken()->getUser();

        if ($this->get("self.right.manager")->checkRight("right.editrightsuser", $currentUser)) {
            $this->get("self.rightgroup.manager")->toggleAll($user, $rightGroup);

            $this->get("session")->getFlashBag()->set('info', "Les permissions ont bien été modifiées");

            return $this->redirect($this->generateUrl('admin_user_rights', array('userId' => $user->getId())));
        }

        return;
    }
}