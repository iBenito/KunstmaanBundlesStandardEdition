<?php

namespace Zizoo\ProfileBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Zizoo\ProfileBundle\Entity\Profile;
use Zizoo\ProfileBundle\Form\ProfileType;

class ProfileController extends Controller
{
    
    /**
     * Get User Information
     * 
     * @return Response
     */
    public function showAction($username) 
    {
        $user   = $this->getDoctrine()->getManager()->getRepository('ZizooUserBundle:User')->findOneByUsername($username);

        return $this->render('ZizooProfileBundle:Profile:show.html.twig', array(
            'user' => $user
        ));
    }
    
    /**
     * Get User Information
     * 
     * @param integer $id
     * @return Response
     */
    public function editAction() 
    {
        $user       = $this->getUser();
        $profile    = $user->getProfile();

        return $this->render('ZizooProfileBundle:Profile:edit.html.twig', array(
            'profile' => $profile
        ));
    }
    
    /**
     * Create form for Profile Edit
     * 
     * @param \Zizoo\ProfileBundle\Entity\Profile $profile
     * @param string $formPath Path for form submission
     * @return Response
     * @throws type
     */
    public function editWidgetAction(Profile $profile, $formPath = 'ZizooProfileBundle_edit')
    {
        if (!$profile) {
            throw $this->createNotFoundException('Unable to find Profile entity.');
        }

        $editForm = $this->createForm(new ProfileType(), $profile);
   
        return $this->render('ZizooProfileBundle:Profile:edit_widget.html.twig',array(
            'profile' => $profile,
            'edit_form' => $editForm->createView(),
            'formPath' => $formPath
        ));
    }
    
    /**
     * Edits an existing Profile entity.
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param integer $id
     * @param string $formPath Path for form submission
     * @return Response
     * @throws type
     */
    public function updateAction(Request $request, $id, $formPath)
    {
        $em = $this->getDoctrine()->getManager();

        $profile = $em->getRepository('ZizooProfileBundle:Profile')->find($id);

        if (!$profile) {
            throw $this->createNotFoundException('Unable to find Profile entity.');
        }

        $editForm = $this->createForm(new ProfileType(), $profile);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            
            //setting the updated field manually for file upload DO NOT REMOVE
            $profile->setUpdated(new \DateTime());
            
            $em->persist($profile);
            $em->flush();
            
            return $this->redirect($this->generateUrl($formPath, array('id' => $id)));
        }

        return $this->render($formPath, array(
            'profile'      => $profile,
            'edit_form'   => $editForm->createView(),
        ));
    }
    
}
?>
