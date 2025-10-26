<?php
session_start();

if (!isset($_SESSION['ticketapp_tickets'])) {
    $_SESSION['ticketapp_tickets'] = [];
}

class TicketApp {

    public static function getTickets() {
        return $_SESSION['ticketapp_tickets'];
    }

    public static function createTicket($data) {
        $errors = self::validate($data);
        if ($errors) return ['errors' => $errors];

        $ticket = [
            'id' => time(),
            'title' => $data['title'],
            'description' => $data['description'] ?? '',
            'status' => $data['status']
        ];
        $_SESSION['ticketapp_tickets'][] = $ticket;
        return ['success' => 'Ticket created successfully!'];
    }

    public static function editTicket($id, $data) {
        $errors = self::validate($data);
        if ($errors) return ['errors' => $errors];

        foreach ($_SESSION['ticketapp_tickets'] as &$ticket) {
            if ($ticket['id'] == $id) {
                $ticket['title'] = $data['title'];
                $ticket['description'] = $data['description'] ?? '';
                $ticket['status'] = $data['status'];
                return ['success' => 'Ticket updated successfully!'];
            }
        }
        return ['errors' => ['general' => 'Ticket not found.']];
    }

    public static function deleteTicket($id) {
        $_SESSION['ticketapp_tickets'] = array_filter($_SESSION['ticketapp_tickets'], fn($t) => $t['id'] != $id);
        return ['success' => 'Ticket deleted successfully!'];
    }

    private static function validate($data) {
        $errors = [];
        if (empty($data['title'])) $errors['title'] = 'Title is required.';
        if (!in_array($data['status'], ['open','in_progress','closed'])) $errors['status'] = 'Status must be Open, In Progress, or Closed.';
        return $errors;
    }
}
