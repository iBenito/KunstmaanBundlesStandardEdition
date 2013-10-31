<?php

namespace Zizoo\ProfileBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Zizoo\BaseBundle\Form\Type\MediaType;

use Zizoo\ProfileBundle\Entity\Profile;
use Zizoo\ProfileBundle\Entity\ProfileAvatar;
use Zizoo\ProfileBundle\Form\ProfileType;

class ProfileController extends Controller
{
    
    /**
     * Get User Information
     * 
     * @return Response
     */
    public function showAction($id)
    {
        $user   = $this->getDoctrine()->getManager()->getRepository('ZizooUserBundle:User')->findOneById($id);

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
    public function editAction(Request $request)
    {
        $user       = $this->getUser();
        $profile    = $user->getProfile();
        
        $routes     = $request->query->get('routes');
        
        $profileType = $this->get('zizoo_profile.profile_type');
        $editForm = $this->createForm($profileType, $profile, array('validation_groups' => 'Default'));

        if ($request->isMethod('post')){
            $editForm->bind($request);

            if ($editForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $profile = $editForm->getData();
                //setting the updated field manually for file upload DO NOT REMOVE
                $profile->setUpdated(new \DateTime());
                $address = $profile->getAddress();
                $em->persist($address);
                $address->setProfile($profile);

                $avatars = $profile->getAvatar();

                $now = new \DateTime();
                foreach ($avatars as $avatar) {
                    $avatar->setUpdated($now);
                    $em->persist($avatar);
                }

                $em->persist($profile);

                $em->flush();
                
                $this->get('session')->setFlash('notice', 'Your profile was updated!');
                return $this->redirect($this->generateUrl($routes['profile_route']));
            }
        }
   
        return $this->render('ZizooProfileBundle:Profile:edit.html.twig',array(
            'edit_form'     => $editForm->createView(),
        ));
    }
    
    public function addAvatarAction()
    {
        $request    = $this->getRequest();
        $user       = $this->getUser();
        $profile    = $user->getProfile();

        $imageFile      = $request->files->get('avatarFile');
        $profileService = $this->container->get('profile_service');

        try {
            $avatar = $profileService->addAvatar($profile, $imageFile, true);
            return new JsonResponse(array('message' => 'Your avatar has been uploaded successfully', 'id' => $avatar->getId()));
        } catch (\Exception $e){
            return new Response($e->getMessage(), 400);
        }
        
    }
    
    public function getAvatarsAction()
    {
        $request    = $this->getRequest();
        $user       = $this->getUser();
        $profile    = $user->getProfile();
                
        $profileType = $this->get('zizoo_profile.profile_type');
        $form = $this->createForm($profileType, $profile);
        
        return $this->render('ZizooProfileBundle:Profile:avatar.html.twig',array(
            'form'     => $form->createView()
        ));
    }
     
}
?>
