<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\ContactMessageModel;
use App\Utils\Route;
use App\Utils\HttpException;
use App\Middlewares\{AuthMiddleware, RoleMiddleware, Roles};

class ContactMessage extends Controller {
  protected object $contact;

  public function __construct($param) {
    $this->contact = new ContactMessageModel();
    parent::__construct($param);
  }

  #[Route("POST", "/contact")]
  public function createContactMessage() {
    $required = ['name', 'email', 'subject', 'message'];
    foreach ($required as $field) {
      if (empty($this->body[$field])) {
        throw new HttpException("Missing field: $field", 400);
      }
    }

    $this->contact->add($this->body);
    return ['message' => 'Contact message successfully sent!'];
  }

  #[Route("GET", "/contact", /*middlewares: [AuthMiddleware::class, [RoleMiddleware::class, Roles::ROLE_ADMIN]]*/)]
  public function getAllContactMessages() {
    return $this->contact->getAll();
  }

  #[Route("GET", "/contact/:id", middlewares: [AuthMiddleware::class, [RoleMiddleware::class, Roles::ROLE_ADMIN]])]
  public function getContactMessage() {
    return $this->contact->get(intval($this->params['id']));
  }

  #[Route("DELETE", "/contact/:id", middlewares: [AuthMiddleware::class, [RoleMiddleware::class, Roles::ROLE_ADMIN]])]
  public function deleteContactMessage() {
    return $this->contact->delete(intval($this->params['id']));
  }
}
