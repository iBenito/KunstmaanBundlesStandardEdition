<?php

namespace Zizoo\ProfileBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Zizoo\BaseBundle\Form\Type\MediaType;

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
        $request    = $this->getRequest();
        $user       = $this->getUser();
        $profile    = $user->getProfile();

        $originalAvatars = array();
        // Create an array of the current ProfileAvatar objects in the database
        foreach ($profile->getAvatar() as $avatar) {
            $originalAvatars[] = $avatar;
        }
        
        $profileType = $this->get('zizoo_profile.profile_type');
        $editForm = $this->createForm($profileType, $profile);
        
        if ($request->isMethod('post')){
            $editForm->bind($request);

            if ($editForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $profile = $editForm->getData();
                //setting the updated field manually for file upload DO NOT REMOVE
                $profile->setUpdated(new \DateTime());

                $avatars = $profile->getAvatar();

                $i = $avatars->count();
                $now = new \DateTime();
                // filter $originalAvatars to contain avatars no longer present
                foreach ($avatars as $avatar) {
                    foreach ($originalAvatars as $key => $toDel) {
                        if ($toDel->getId() === $avatar->getId()) {
                            unset($originalAvatars[$key]);
                        } 
                    }
                    $avatar->setUpdated($now);
                    $em->persist($avatar);
                }

                // remove the relationship between the avatar and the profile
                foreach ($originalAvatars as $avatar) {
                    // remove the ProvileAvatar from the Profile
                    $profile->removeAvatar($avatar);

                    // remove the avatar completely
                    $em->remove($avatar);
                }

                $em->persist($profile);

                $em->flush();
                return $this->redirect($this->generateUrl('ZizooProfileBundle_Profile_Edit'));
            }
        }
   
        return $this->render('ZizooProfileBundle:Profile:edit_test.html.twig',array(
            'edit_form'     => $editForm->createView()
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
    public function editWidgetAction(Profile $profile, $formPath = 'ZizooProfileBundle_Profile_Edit', $viewTemplate = 'ZizooProfileBundle:Profile:edit.html.twig')
    {
        if (!$profile) {
            throw $this->createNotFoundException('Unable to find Profile entity.');
        }

        $editForm = $this->createForm(new ProfileType(), $profile);
   
        return $this->render('ZizooProfileBundle:Profile:edit_widget.html.twig',array(
            'profile'       => $profile,
            'edit_form'     => $editForm->createView(),
            'formPath'      => $formPath,
            'viewTemplate'  => $viewTemplate
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
    public function updateAction(Request $request, $id, $formPath, $viewTemplate)
    {
        $em = $this->getDoctrine()->getManager();

        $profile = $em->getRepository('ZizooProfileBundle:Profile')->find($id);
        
        $originalAvatars = array();
        // Create an array of the current ProfileAvatar objects in the database
        foreach ($profile->getAvatar() as $avatar) {
            $originalAvatars[] = $avatar;
        }
        
        if (!$profile) {
            throw $this->createNotFoundException('Unable to find Profile entity.');
        }

        $editForm = $this->createForm(new ProfileType(), $profile);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $profile = $editForm->getData();
            //setting the updated field manually for file upload DO NOT REMOVE
            $profile->setUpdated(new \DateTime());
            
            $avatars = $profile->getAvatar();
            
            $i = $avatars->count();
            $now = new \DateTime();
            // filter $originalAvatars to contain avatars no longer present
            foreach ($avatars as $avatar) {
                foreach ($originalAvatars as $key => $toDel) {
                    if ($toDel->getId() === $avatar->getId()) {
                        unset($originalAvatars[$key]);
                    } 
                }
                //$avatar->setOrder(--$i);
                $avatar->setUpdated($now);
                $em->persist($avatar);
            }
            
            // remove the relationship between the avatar and the profile
            foreach ($originalAvatars as $avatar) {
                // remove the ProvileAvatar from the Profile
                $profile->removeAvatar($avatar);

                // remove the avatar completely
                $em->remove($avatar);
            }
            
            $em->persist($profile);
            
            $em->flush();
            return $this->redirect($this->generateUrl($formPath));
        }

        return $this->render($viewTemplate, array(
            'profile'       => $profile,
            'edit_form'     => $editForm->createView(),
            'formPath'      => $formPath,
            'viewTemplate'  => $viewTemplate,
            'form_path'    => $formPath
        ));
    }
    
    public function singleMediaAction()
    {
        $user = $this->getUser();
        $profile = $user->getProfile();
        $avatar  = $profile->getAvatar();
        
        $singleTest = new \Zizoo\ProfileBundle\Entity\SingleTest($avatar->first());
        
        $form = $this->createForm(new \Zizoo\ProfileBundle\Form\Type\SingleTestType(), $singleTest, array());
        
        return $this->render('ZizooProfileBundle:Profile:test/zizoo_media.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
    public function collectionMediaAction()
    {
        $user = $this->getUser();
        $profile = $user->getProfile();
        $avatar  = $profile->getAvatar();
        
        $collectionTest = new \Zizoo\ProfileBundle\Entity\CollectionTest();
        foreach ($avatar as $a){
            $collectionTest->addAvatar($a);
        }
        
        $form = $this->createForm(new \Zizoo\ProfileBundle\Form\Type\CollectionTestType(), $collectionTest, array());
        
        return $this->render('ZizooProfileBundle:Profile:test/zizoo_media_collection.html.twig', array(
            'form' => $form->createView()
        ));
    }
    
}
?>
