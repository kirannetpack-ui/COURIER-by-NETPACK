<?php

namespace App\Services;

use App\Models\PickupRequest;
use App\Models\DeliveryReminder;
use App\Models\ReminderLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReminderService
{
    /**
     * Service timeframes in hours
     */
    protected $serviceTimeframes = [
        'ecommerce' => 1,
        'flash' => 4,
        'same_day' => 12,
        'standard' => 72,
        'himalayan' => 168,
    ];

    /**
     * Service labels
     */
    protected $serviceLabels = [
        'ecommerce' => 'E-commerce (1 hour)',
        'flash' => 'Flash (2-4 hours)',
        'same_day' => 'Same Day (by 8 PM)',
        'standard' => 'Standard (1-3 days)',
        'himalayan' => 'Himalayan (3-7 days)',
    ];

    /**
     * Schedule reminders for a pickup request
     */
    public function scheduleReminders(PickupRequest $pickup)
    {
        // Clear existing reminders for this pickup
        DeliveryReminder::where('pickup_request_id', $pickup->id)->delete();
        
        $serviceTier = $pickup->service_tier ?? 'standard';
        
        switch ($serviceTier) {
            case 'ecommerce':
                $this->scheduleEcommerceReminders($pickup);
                break;
            case 'flash':
                $this->scheduleFlashReminders($pickup);
                break;
            case 'same_day':
                $this->scheduleSameDayReminders($pickup);
                break;
            case 'standard':
                $this->scheduleStandardReminders($pickup);
                break;
            case 'himalayan':
                $this->scheduleHimalayanReminders($pickup);
                break;
            default:
                $this->scheduleStandardReminders($pickup);
                break;
        }
        
        Log::info('Reminders scheduled for pickup', [
            'pickup_id' => $pickup->id,
            'service_tier' => $serviceTier,
            'tracking_number' => $pickup->tracking_number
        ]);
    }

    /**
     * E-commerce: 1 hour delivery - Reminder 30 minutes before
     */
    private function scheduleEcommerceReminders($pickup)
    {
        $pickupTime = Carbon::parse($pickup->scheduled_pickup_time ?? $pickup->created_at);
        $deadline = $pickupTime->copy()->addHours(1);
        
        $reminderTime = $deadline->copy()->subMinutes(30);
        
        if ($reminderTime->isFuture()) {
            $this->createReminder($pickup->id, 'ecommerce', 'partner', 1, $reminderTime);
            $this->createReminder($pickup->id, 'ecommerce', 'admin', 1, $reminderTime);
        }
    }

    /**
     * Flash: 2-4 hours delivery - Reminder 1 hour before max time
     */
    private function scheduleFlashReminders($pickup)
    {
        $maxHours = 4;
        $deadline = Carbon::parse($pickup->scheduled_pickup_time ?? $pickup->created_at)->addHours($maxHours);
        
        $reminderTime = $deadline->copy()->subHours(1);
        
        if ($reminderTime->isFuture()) {
            $this->createReminder($pickup->id, 'flash', 'partner', 1, $reminderTime);
            $this->createReminder($pickup->id, 'flash', 'admin', 1, $reminderTime);
        }
    }

    /**
     * Same Day: By 8 PM - Reminders 2 hours and 1 hour before
     */
    private function scheduleSameDayReminders($pickup)
    {
        $deadline = Carbon::parse($pickup->scheduled_pickup_time ?? $pickup->created_at)->setTime(20, 0);
        
        $reminder1Time = $deadline->copy()->subHours(2);
        if ($reminder1Time->isFuture()) {
            $this->createReminder($pickup->id, 'same_day', 'partner', 1, $reminder1Time);
            $this->createReminder($pickup->id, 'same_day', 'admin', 1, $reminder1Time);
        }
        
        $reminder2Time = $deadline->copy()->subHours(1);
        if ($reminder2Time->isFuture()) {
            $this->createReminder($pickup->id, 'same_day', 'partner', 2, $reminder2Time);
            $this->createReminder($pickup->id, 'same_day', 'admin', 2, $reminder2Time);
        }
    }

    /**
     * Standard: 1-3 days - Reminders: 1 day before & on last day at 2PM
     */
    private function scheduleStandardReminders($pickup)
    {
        $maxDays = 3;
        $deadline = Carbon::parse($pickup->scheduled_pickup_time ?? $pickup->created_at)->addDays($maxDays);
        
        $reminder1Time = $deadline->copy()->subDays(1)->setTime(10, 0);
        if ($reminder1Time->isFuture()) {
            $this->createReminder($pickup->id, 'standard', 'partner', 1, $reminder1Time);
            $this->createReminder($pickup->id, 'standard', 'admin', 1, $reminder1Time);
        }
        
        $reminder2Time = $deadline->copy()->setTime(14, 0);
        if ($reminder2Time->isFuture()) {
            $this->createReminder($pickup->id, 'standard', 'partner', 2, $reminder2Time);
            $this->createReminder($pickup->id, 'standard', 'admin', 2, $reminder2Time);
        }
    }

    /**
     * Himalayan: 3-7 days - Reminders: 2 days, 1 day before, last day 2PM
     */
    private function scheduleHimalayanReminders($pickup)
    {
        $maxDays = 7;
        $deadline = Carbon::parse($pickup->scheduled_pickup_time ?? $pickup->created_at)->addDays($maxDays);
        
        $reminder1Time = $deadline->copy()->subDays(2)->setTime(10, 0);
        if ($reminder1Time->isFuture()) {
            $this->createReminder($pickup->id, 'himalayan', 'partner', 1, $reminder1Time);
            $this->createReminder($pickup->id, 'himalayan', 'admin', 1, $reminder1Time);
        }
        
        $reminder2Time = $deadline->copy()->subDays(1)->setTime(10, 0);
        if ($reminder2Time->isFuture()) {
            $this->createReminder($pickup->id, 'himalayan', 'partner', 2, $reminder2Time);
            $this->createReminder($pickup->id, 'himalayan', 'admin', 2, $reminder2Time);
        }
        
        $reminder3Time = $deadline->copy()->setTime(14, 0);
        if ($reminder3Time->isFuture()) {
            $this->createReminder($pickup->id, 'himalayan', 'partner', 3, $reminder3Time);
            $this->createReminder($pickup->id, 'himalayan', 'admin', 3, $reminder3Time);
        }
    }

    /**
     * Create a reminder record
     */
    private function createReminder($pickupId, $serviceTier, $reminderType, $number, $scheduledAt)
    {
        if ($scheduledAt->isPast()) {
            return;
        }
        
        $message = $this->getReminderMessage($serviceTier, $reminderType, $number);
        
        DeliveryReminder::create([
            'pickup_request_id' => $pickupId,
            'service_tier' => $serviceTier,
            'reminder_type' => $reminderType,
            'reminder_number' => $number,
            'scheduled_at' => $scheduledAt,
            'is_sent' => false,
            'message' => $message
        ]);
    }

    /**
     * Get reminder message based on service and type
     */
    private function getReminderMessage($serviceTier, $reminderType, $number)
    {
        $serviceLabel = $this->serviceLabels[$serviceTier] ?? 'Standard';
        $urgency = $number >= 2 ? '⚠️ URGENT: ' : '';
        
        if ($reminderType === 'partner') {
            return "{$urgency}REMINDER #{$number}: Your {$serviceLabel} delivery is approaching the deadline. Please ensure timely delivery and update status.";
        } elseif ($reminderType === 'admin') {
            return "{$urgency}ALERT #{$number}: A {$serviceLabel} delivery is at risk of delay. Please follow up with the partner.";
        } else {
            return "{$urgency}REMINDER #{$number}: Your {$serviceLabel} delivery is approaching the deadline.";
        }
    }

    /**
     * Process pending reminders (run by cron job)
     */
    public function processPendingReminders()
    {
        $reminders = DeliveryReminder::where('is_sent', false)
            ->where('scheduled_at', '<=', now())
            ->get();
        
        $processed = 0;
        
        foreach ($reminders as $reminder) {
            $pickup = PickupRequest::find($reminder->pickup_request_id);
            
            if (!$pickup || in_array($pickup->status, ['delivered', 'cancelled'])) {
                $reminder->delete();
                continue;
            }
            
            if ($pickup->status === 'out_for_delivery') {
                $reminder->delete();
                continue;
            }
            
            $this->sendReminder($reminder, $pickup);
            
            $reminder->update([
                'is_sent' => true,
                'sent_at' => now()
            ]);
            
            $processed++;
        }
        
        Log::info('Processed pending reminders', ['count' => $processed]);
        return $processed;
    }

    /**
     * Send a reminder notification
     */
    private function sendReminder($reminder, $pickup)
    {
        if ($reminder->reminder_type === 'partner') {
            $this->notifyPartner($pickup, $reminder);
        } elseif ($reminder->reminder_type === 'admin') {
            $this->notifyAdmin($pickup, $reminder);
        } elseif ($reminder->reminder_type === 'customer') {
            $this->notifyCustomer($pickup, $reminder);
        }
    }

    /**
     * Notify partner about the reminder
     */
    private function notifyPartner($pickup, $reminder)
    {
        $partner = $pickup->partner;
        if (!$partner) return;
        
        $phone = $pickup->customer_phone ?? 'N/A';
        $message = $reminder->message . "\n\n📦 Order ID: #{$pickup->id}\n👤 Customer: {$pickup->customer_name}\n📍 Delivery Address: {$pickup->delivery_address}\n📱 Phone: {$phone}";
        
        ReminderLog::create([
            'pickup_request_id' => $pickup->id,
            'reminder_id' => $reminder->id,
            'reminder_type' => 'partner',
            'sent_to' => $partner->email,
            'message' => $message,
            'channel' => 'email',
            'status' => 'sent',
            'sent_at' => now(),
            'metadata' => [
                'reminder_number' => $reminder->reminder_number,
                'service_tier' => $reminder->service_tier,
            ]
        ]);
        
        if ($reminder->reminder_number === 1 && !$pickup->customer_notified) {
            $this->notifyCustomerWithContact($pickup);
        }
    }

    /**
     * Notify admin about the reminder
     */
    private function notifyAdmin($pickup, $reminder)
    {
        $admins = User::whereIn('user_type', ['admin', 'super_admin'])->get();
        
        $message = $reminder->message . "\n\n📦 Order ID: #{$pickup->id}\n🤝 Partner: " . ($pickup->partner->name ?? 'Not assigned') . "\n👤 Customer: {$pickup->customer_name}\n📍 Delivery Address: {$pickup->delivery_address}";
        
        foreach ($admins as $admin) {
            ReminderLog::create([
                'pickup_request_id' => $pickup->id,
                'reminder_id' => $reminder->id,
                'reminder_type' => 'admin',
                'sent_to' => $admin->email,
                'message' => $message,
                'channel' => 'email',
                'status' => 'sent',
                'sent_at' => now(),
                'metadata' => [
                    'reminder_number' => $reminder->reminder_number,
                    'service_tier' => $reminder->service_tier,
                    'admin_id' => $admin->id,
                ]
            ]);
        }
    }

    /**
     * Notify customer with partner contact information
     */
    private function notifyCustomerWithContact($pickup)
    {
        $partner = $pickup->partner;
        if (!$partner) return;
        
        $customer = User::find($pickup->seller_id);
        if (!$customer) return;
        
        $partnerPhone = $partner->phone ?? 'N/A';
        $partnerContact = $partner->contact_person ?? $partner->name ?? 'Partner';
        
        $message = "Dear Customer,\n\nYour order #{$pickup->id} is being processed. For any questions about your delivery, please contact:\n\n";
        $message .= "🤝 Partner: {$partner->name}\n";
        $message .= "👤 Contact Person: {$partnerContact}\n";
        $message .= "📱 Phone: {$partnerPhone}\n\n";
        $message .= "📦 Tracking Number: {$pickup->tracking_number}\n";
        $message .= "⏰ Expected Delivery: " . $this->getExpectedDeliveryText($pickup) . "\n\n";
        $message .= "Thank you for choosing NetPack!";
        
        ReminderLog::create([
            'pickup_request_id' => $pickup->id,
            'reminder_id' => null,
            'reminder_type' => 'customer',
            'sent_to' => $customer->email,
            'message' => $message,
            'channel' => 'email',
            'status' => 'sent',
            'sent_at' => now(),
            'metadata' => [
                'partner_id' => $partner->id,
                'partner_name' => $partner->name,
            ]
        ]);
        
        $pickup->update([
            'customer_notified' => true,
            'customer_notification_message' => $message,
            'contact_person_name' => $partnerContact,
            'contact_person_phone' => $partnerPhone
        ]);
    }

    /**
     * Notify customer about delay
     */
    private function notifyCustomer($pickup, $reminder)
    {
        $customer = User::find($pickup->seller_id);
        if (!$customer) return;
        
        $partnerName = $pickup->partner->name ?? 'Partner';
        $partnerPhone = $pickup->partner->phone ?? 'N/A';
        
        $message = "Dear Customer,\n\nWe regret to inform you that your order #{$pickup->id} is experiencing a delay. Our team is working to resolve this as quickly as possible.\n\n";
        $message .= "For updates, please contact:\n";
        $message .= "🤝 Partner: {$partnerName}\n";
        $message .= "📱 Phone: {$partnerPhone}\n\n";
        $message .= "We apologize for any inconvenience caused.\n";
        $message .= "Thank you for your patience.";
        
        ReminderLog::create([
            'pickup_request_id' => $pickup->id,
            'reminder_id' => $reminder->id,
            'reminder_type' => 'customer',
            'sent_to' => $customer->email,
            'message' => $message,
            'channel' => 'email',
            'status' => 'sent',
            'sent_at' => now(),
            'metadata' => [
                'delay_reported' => true,
            ]
        ]);
    }

    /**
     * Get expected delivery text based on service tier
     */
    private function getExpectedDeliveryText($pickup)
    {
        $serviceTier = $pickup->service_tier ?? 'standard';
        
        $texts = [
            'ecommerce' => 'Within 1 hour',
            'flash' => '2-4 hours',
            'same_day' => 'Today by 8 PM',
            'standard' => '1-3 days',
            'himalayan' => '3-7 days',
        ];
        
        return $texts[$serviceTier] ?? 'As scheduled';
    }

    /**
     * Schedule reminders for all pending pickups
     */
    public function scheduleAllReminders()
    {
        $pickups = PickupRequest::whereIn('status', ['pending', 'confirmed', 'picked_up', 'in_transit'])
            ->where('is_delayed', false)
            ->get();
        
        $count = 0;
        foreach ($pickups as $pickup) {
            if ($pickup->deliveryReminders()->count() === 0) {
                $this->scheduleReminders($pickup);
                $count++;
            }
        }
        
        Log::info('Scheduled reminders for all pending pickups', ['count' => $count]);
        return $count;
    }
}