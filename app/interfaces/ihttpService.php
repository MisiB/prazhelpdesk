<?php

namespace App\interfaces;

interface ihttpService
{
    public function getsettings();

    public function gettickets($email);

    public function getticket($id);

    public function createTicket($ticket);

    public function updateTicket($id, $ticket);

    public function deleteTicket($id);


    public function closeTicket($ticketId);
    public function addcomment($comment);
    public function updatecomment($commentid, $comment);
    public function deletecomment($commentid);

    public function searchKnowledgeBase($query);
    public function getKnowledgeBaseArticles($params = []);
    public function getKnowledgeBaseArticle($id);
    public function getKnowledgeBaseCategories();
}
