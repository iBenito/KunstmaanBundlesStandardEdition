<?php

namespace Zizoo\ProfileBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

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
    public function editAction(Request $request)
    {
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
                return $this->redirect($this->generateUrl('ZizooBaseBundle_Dashboard_Profile'));
            }
        }
   
        return $this->render('ZizooProfileBundle:Profile:edit.html.twig',array(
            'edit_form'     => $editForm->createView()
        ));
    }
    
    public function addAvatarAction()
    {
        $request    = $this->getRequest();
        $user       = $this->getUser();
        $profile    = $user->getProfile();
        
        $em = $this->getDoctrine()->getManager();
        $avatarFile = $request->files->get('avatarFile');
        
        $avatar = new ProfileAvatar();
        $avatar->setPath($avatarFile->guessExtension());
        $avatar->setMimeType($avatarFile->getMimeType());
        
        $avatar->setProfile($profile);
        $profile->addAvatar($avatar);

        $em->persist($avatar);
        
        $validator          = $this->get('validator');
        $profileErrors      = $validator->validate($profile, 'default');
        $avatarErrors       = $validator->validate($avatar, 'default');
        $numProfileErrors   = $profileErrors->count();
        $numAvatarErrors    = $avatarErrors->count();
        
        if ($numProfileErrors==0 && $numAvatarErrors==0){
            $em->flush();

            $avatarFile->move(
                $avatar->getUploadRootDir(),
                $avatar->getId().'.'.$avatar->getPath()
            );
            
            return new JSONResponse(array('message' => 'Your avatar has been uploaded successfully', 'id' => $avatar->getId()));
        } else {
            $errorArr = array();
            for ($i=0; $i<$numProfileErrors; $i++){
                $error = $profileErrors->get($i);
                $msgTemplate = $error->getMessage();
                $errorArr[] = $msgTemplate;
            }
            for ($i=0; $i<$numAvatarErrors; $i++){
                $error = $avatarErrors->get($i);
                $msgTemplate = $error->getMessage();
                $errorArr[] = $msgTemplate;
            }
            return new Response(join(',', $errorArr), 400);
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
