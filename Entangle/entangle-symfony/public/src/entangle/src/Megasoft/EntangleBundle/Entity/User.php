<?php

namespace Megasoft\EntangleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class User {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="photo", type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @var string
     *
     * @ORM\Column(name="userBio", type="string", length=255, nullable=true)
     */
    private $userBio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthDate", type="date" , nullable=true)
     */
    private $birthDate;

    /**
     *
     * @var boolean 
     * @ORM\Column(name="acceptMailNotifications", type="boolean" , columnDefinition="tinyint(1) DEFAULT 1")
     */
    private $acceptMailNotifications = true;

    /**
     * @var Notification[]
     * 
     * @ORM\OneToMany(targetEntity="Notification", mappedBy="user", cascade={"persist"})
     */
    private $notifications;

    /**
     *
     * @var Claim[]
     * @ORM\OneToMany(targetEntity="Claim", mappedBy="claimer", cascade={"persist"})
     */
    private $claims;

    /**
     *
     * @var Session[]
     * @ORM\OneToMany(targetEntity="Session", mappedBy="user", cascade={"persist"})
     */
    private $sessions;

    /**
     * @var Message[]
     * 
     * @ORM\OneToMany(targetEntity="Message", mappedBy="sender", cascade={"persist"})
     */
    private $messages;

    /**
     * @var Request[]
     * 
     * @ORM\OneToMany(targetEntity="Request", mappedBy="user", cascade={"persist"})
     */
    private $requests;

    /**
     * @var Offer[]
     * 
     * @ORM\OneToMany(targetEntity="Offer", mappedBy="user", cascade={"persist"})
     */
    private $offers;

    /**
     * @var UserTangle[]
     * 
     * @ORM\OneToMany(targetEntity="UserTangle", mappedBy="user", cascade={"persist"})
     */
    private $userTangles;

    /**
     * @var UserEmail[]
     * 
     * @ORM\OneToMany(targetEntity="UserEmail", mappedBy="user", cascade={"persist"})
     */
    private $emails;

    /**
     * @var InvitationCode[]
     * 
     * @ORM\OneToMany(targetEntity="InvitationCode", mappedBy="inviter", cascade={"persist"})
     */
    private $invitations;

    /**
     * @var InvitationCode[]
     * 
     * @ORM\OneToMany(targetEntity="InvitationCode", mappedBy="user", cascade={"persist"})
     */
    private $invitationCodes;

    /**
     * @var PendingInvitation[]
     * 
     * @ORM\OneToMany(targetEntity="PendingInvitation", mappedBy="invitee", cascade={"persist"})
     */
    private $pendingInvitationInvitees;

    /**
     * @var PendingInvitation[]
     * 
     * @ORM\OneToMany(targetEntity="PendingInvitation", mappedBy="inviter", cascade={"persist"})
     */
    private $pendingInvitationInviters;

    /**
     * @var UnfreezeRequest[]
     * 
     * @ORM\OneToMany(targetEntity="UnfreezeRequest", mappedBy="user", cascade={"persist"})
     */
    private $unfreezeRequests;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set photo
     *
     * @param string $photo
     * @return User
     */
    public function setPhoto($photo) {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string 
     */
    public function getPhoto() {
        return $this->photo;
    }

    /**
     * Set userBio
     *
     * @param string $userBio
     * @return User
     */
    public function setUserBio($userBio) {
        $this->userBio = $userBio;

        return $this;
    }

    /**
     * Get userBio
     *
     * @return string 
     */
    public function getUserBio() {
        return $this->userBio;
    }

    /**
     * Set birthDate
     *
     * @param \DateTime $birthDate
     * @return User
     */
    public function setBirthDate($birthDate) {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime 
     */
    public function getBirthDate() {
        return $this->birthDate;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->notifications = new \Doctrine\Common\Collections\ArrayCollection();
        $this->claims = new \Doctrine\Common\Collections\ArrayCollection();
        $this->messages = new \Doctrine\Common\Collections\ArrayCollection();
        $this->requests = new \Doctrine\Common\Collections\ArrayCollection();
        $this->userTangles = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add notifications
     *
     * @param \Megasoft\EntangleBundle\Entity\Notification $notifications
     * @return User
     */
    public function addNotification(\Megasoft\EntangleBundle\Entity\Notification $notifications) {
        $this->notifications[] = $notifications;
        $notifications->setUser($this);
        return $this;
    }

    /**
     * Remove notifications
     *
     * @param \Megasoft\EntangleBundle\Entity\Notification $notifications
     */
    public function removeNotification(\Megasoft\EntangleBundle\Entity\Notification $notifications) {
        $this->notifications->removeElement($notifications);
    }

    /**
     * Get notifications
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotifications() {
        return $this->notifications;
    }

    /**
     * Add claims
     *
     * @param \Megasoft\EntangleBundle\Entity\Claim $claims
     * @return User
     */
    public function addClaim(\Megasoft\EntangleBundle\Entity\Claim $claims) {
        $this->claims[] = $claims;
        $claims->setUser($this);
        return $this;
    }

    /**
     * Remove claims
     *
     * @param \Megasoft\EntangleBundle\Entity\Claim $claims
     */
    public function removeClaim(\Megasoft\EntangleBundle\Entity\Claim $claims) {
        $this->claims->removeElement($claims);
    }

    /**
     * Get claims
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getClaims() {
        return $this->claims;
    }

    /**
     * Add messages
     *
     * @param \Megasoft\EntangleBundle\Entity\Message $messages
     * @return User
     */
    public function addMessage(\Megasoft\EntangleBundle\Entity\Message $messages) {
        $this->messages[] = $messages;
        $messages->setUser($this);
        return $this;
    }

    /**
     * Remove messages
     *
     * @param \Megasoft\EntangleBundle\Entity\Message $messages
     */
    public function removeMessage(\Megasoft\EntangleBundle\Entity\Message $messages) {
        $this->messages->removeElement($messages);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMessages() {
        return $this->messages;
    }

    /**
     * Add requests
     *
     * @param \Megasoft\EntangleBundle\Entity\Request $requests
     * @return User
     */
    public function addRequest(\Megasoft\EntangleBundle\Entity\Request $requests) {
        $this->requests[] = $requests;
        $requests->setUser($this);
        return $this;
    }

    /**
     * Remove requests
     *
     * @param \Megasoft\EntangleBundle\Entity\Request $requests
     */
    public function removeRequest(\Megasoft\EntangleBundle\Entity\Request $requests) {
        $this->requests->removeElement($requests);
    }

    /**
     * Get requests
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRequests() {
        return $this->requests;
    }

    /**
     * Add userTangles
     *
     * @param \Megasoft\EntangleBundle\Entity\UserTangle $userTangles
     * @return User
     */
    public function addUserTangle(\Megasoft\EntangleBundle\Entity\UserTangle $userTangles) {
        $this->userTangles[] = $userTangles;
        $userTangles->setUser($this);
        return $this;
    }

    /**
     * Remove userTangles
     *
     * @param \Megasoft\EntangleBundle\Entity\UserTangle $userTangles
     */
    public function removeUserTangle(\Megasoft\EntangleBundle\Entity\UserTangle $userTangles) {
        $this->userTangles->removeElement($userTangles);
    }

    /**
     * Get userTangles
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserTangles() {
        return $this->userTangles;
    }

    /**
     * Get Tangles
     *
     * @return Tangle[]
     */
    public function getTangles() {
        $tangles = array();
        foreach ($this->userTangles as $userTangle) {
            $tangles[] = $userTangle->getTangle();
        }
        return $tangles;
    }

    /**
     * Add emails
     *
     * @param \Megasoft\EntangleBundle\Entity\UserEmail $emails
     * @return User
     */
    public function addEmail(\Megasoft\EntangleBundle\Entity\UserEmail $emails) {
        $this->emails[] = $emails;
        $emails->setUser($this);
        return $this;
    }

    /**
     * Remove emails
     *
     * @param \Megasoft\EntangleBundle\Entity\UserEmail $emails
     */
    public function removeEmail(\Megasoft\EntangleBundle\Entity\UserEmail $emails) {
        $this->emails->removeElement($emails);
    }

    /**
     * Get emails
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEmails() {
        return $this->emails;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password) {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Add sessions
     *
     * @param \Megasoft\EntangleBundle\Entity\Session $sessions
     * @return User
     */
    public function addSession(\Megasoft\EntangleBundle\Entity\Session $sessions) {
        $this->sessions[] = $sessions;
        $sessions->setUser($this);
        return $this;
    }

    /**
     * Remove sessions
     *
     * @param \Megasoft\EntangleBundle\Entity\Session $sessions
     */
    public function removeSession(\Megasoft\EntangleBundle\Entity\Session $sessions) {
        $this->sessions->removeElement($sessions);
    }

    /**
     * Get sessions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSessions() {
        return $this->sessions;
    }

    /**
     * Add offers
     *
     * @param \Megasoft\EntangleBundle\Entity\Offer $offers
     * @return User
     */
    public function addOffer(\Megasoft\EntangleBundle\Entity\Offer $offers) {
        $this->offers[] = $offers;
        $offers->setUser($this);
        return $this;
    }

    /**
     * Remove offers
     *
     * @param \Megasoft\EntangleBundle\Entity\Offer $offers
     */
    public function removeOffer(\Megasoft\EntangleBundle\Entity\Offer $offers) {
        $this->offers->removeElement($offers);
    }

    /**
     * Get offers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOffers() {
        return $this->offers;
    }

    /**
     * Add invitations
     *
     * @param \Megasoft\EntangleBundle\Entity\InvitationCode $invitations
     * @return User
     */
    public function addInvitation(\Megasoft\EntangleBundle\Entity\InvitationCode $invitations) {
        $this->invitations[] = $invitations;
        $invitations->setInviter($this);
        return $this;
    }

    /**
     * Remove invitations
     *
     * @param \Megasoft\EntangleBundle\Entity\InvitationCode $invitations
     */
    public function removeInvitation(\Megasoft\EntangleBundle\Entity\InvitationCode $invitations) {
        $this->invitations->removeElement($invitations);
    }

    /**
     * Get invitations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInvitations() {
        return $this->invitations;
    }

    /**
     * Add invitationCodes
     *
     * @param \Megasoft\EntangleBundle\Entity\InvitationCode $invitationCodes
     * @return User
     */
    public function addInvitationCode(\Megasoft\EntangleBundle\Entity\InvitationCode $invitationCodes) {
        $this->invitationCodes[] = $invitationCodes;
        $invitationCodes->setUser($this);
        return $this;
    }

    /**
     * Remove invitationCodes
     *
     * @param \Megasoft\EntangleBundle\Entity\InvitationCode $invitationCodes
     */
    public function removeInvitationCode(\Megasoft\EntangleBundle\Entity\InvitationCode $invitationCodes) {
        $this->invitationCodes->removeElement($invitationCodes);
    }

    /**
     * Get invitationCodes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInvitationCodes() {
        return $this->invitationCodes;
    }

    /**
     * Add pendingInvitationInvitees
     *
     * @param \Megasoft\EntangleBundle\Entity\PendingInvitation $pendingInvitationInvitees
     * @return User
     */
    public function addPendingInvitationInvitee(\Megasoft\EntangleBundle\Entity\PendingInvitation $pendingInvitationInvitees) {
        $this->pendingInvitationInvitees[] = $pendingInvitationInvitees;
        $pendingInvitationInvitees - setUser($this);
        return $this;
    }

    /**
     * Remove pendingInvitationInvitees
     *
     * @param \Megasoft\EntangleBundle\Entity\PendingInvitation $pendingInvitationInvitees
     */
    public function removePendingInvitationInvitee(\Megasoft\EntangleBundle\Entity\PendingInvitation $pendingInvitationInvitees) {
        $this->pendingInvitationInvitees->removeElement($pendingInvitationInvitees);
    }

    /**
     * Get pendingInvitationInvitees
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPendingInvitationInvitees() {
        return $this->pendingInvitationInvitees;
    }

    /**
     * Add pendingInvitationInviters
     *
     * @param \Megasoft\EntangleBundle\Entity\PendingInvitation $pendingInvitationInviters
     * @return User
     */
    public function addPendingInvitationInviter(\Megasoft\EntangleBundle\Entity\PendingInvitation $pendingInvitationInviters) {
        $this->pendingInvitationInviters[] = $pendingInvitationInviters;
        $pendingInvitationInviters->setUser($this);
        return $this;
    }

    /**
     * Remove pendingInvitationInviters
     *
     * @param \Megasoft\EntangleBundle\Entity\PendingInvitation $pendingInvitationInviters
     */
    public function removePendingInvitationInviter(\Megasoft\EntangleBundle\Entity\PendingInvitation $pendingInvitationInviters) {
        $this->pendingInvitationInviters->removeElement($pendingInvitationInviters);
    }

    /**
     * Get pendingInvitationInviters
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPendingInvitationInviters() {
        return $this->pendingInvitationInviters;
    }

    /**
     * Add unfreezeRequests
     *
     * @param \Megasoft\EntangleBundle\Entity\UnfreezeRequest $unfreezeRequests
     * @return User
     */
    public function addUnfreezeRequest(\Megasoft\EntangleBundle\Entity\UnfreezeRequest $unfreezeRequests) {
        $this->unfreezeRequests[] = $unfreezeRequests;
        $unfreezeRequests->setUser($this);
        return $this;
    }

    /**
     * Remove unfreezeRequests
     *
     * @param \Megasoft\EntangleBundle\Entity\UnfreezeRequest $unfreezeRequests
     */
    public function removeUnfreezeRequest(\Megasoft\EntangleBundle\Entity\UnfreezeRequest $unfreezeRequests) {
        $this->unfreezeRequests->removeElement($unfreezeRequests);
    }

    /**
     * Get unfreezeRequests
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUnfreezeRequests() {
        return $this->unfreezeRequests;
    }

    /**
     * Set acceptMailNotifications
     *
     * @param boolean $acceptMailNotifications
     * @return User
     */
    public function setAcceptMailNotifications($acceptMailNotifications) {
        $this->acceptMailNotifications = $acceptMailNotifications;

        return $this;
    }

    /**
     * Get acceptMailNotifications
     *
     * @return boolean 
     */
    public function getAcceptMailNotifications() {
        return $this->acceptMailNotifications;
    }

}
