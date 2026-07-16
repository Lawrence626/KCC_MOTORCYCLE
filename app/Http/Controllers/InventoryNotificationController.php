<?php

namespace App\Http\Controllers;

use App\Services\InventoryAlertService;
use Illuminate\Http\Request;

class InventoryNotificationController extends Controller
{
    protected InventoryAlertService $alertService;

    public function __construct(InventoryAlertService $alertService)
    {
        $this->alertService = $alertService;
    }

    /**
     * Get dashboard alerts (active, not dismissed).
     */
    public function dashboardAlerts()
    {
        $alerts = $this->alertService->getDashboardAlerts();

        return response()->json([
            'alerts' => $this->alertService->formatNotifications($alerts),
            'count' => $alerts->count(),
        ]);
    }

    /**
     * Get all notifications for notification center.
     */
    public function index(Request $request)
    {
        $limit = (int) $request->query('limit', 50);
        $notifications = $this->alertService->getNotificationCenterItems($limit);

        return response()->json([
            'notifications' => $this->alertService->formatNotifications($notifications),
            'unread_count' => $this->alertService->getUnreadCount(),
        ]);
    }

    /**
     * Get unread count for notification badge.
     */
    public function unreadCount()
    {
        return response()->json([
            'unread_count' => $this->alertService->getUnreadCount(),
        ]);
    }

    /**
     * Dismiss an alert from dashboard (keeps in notification center).
     */
    public function dismiss(int $id)
    {
        $result = $this->alertService->dismissAlert($id);

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Alert dismissed from dashboard.']);
        }

        return response()->json(['success' => false, 'message' => 'Alert not found or already dismissed.'], 404);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(int $id)
    {
        $result = $this->alertService->markAsRead($id);

        if ($result) {
            return response()->json(['success' => true, 'message' => 'Notification marked as read.']);
        }

        return response()->json(['success' => false, 'message' => 'Notification not found or already read.'], 404);
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead()
    {
        $count = $this->alertService->markAllAsRead();

        return response()->json([
            'success' => true,
            'message' => "{$count} notifications marked as read.",
            'marked_count' => $count,
        ]);
    }

    /**
     * Manually trigger alert sync.
     */
    public function sync()
    {
        $this->alertService->syncAlerts();

        return response()->json([
            'success' => true,
            'message' => 'Inventory alerts synced.',
            'unread_count' => $this->alertService->getUnreadCount(),
        ]);
    }
}
