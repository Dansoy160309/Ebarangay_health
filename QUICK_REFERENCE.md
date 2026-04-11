# 🎨 REMINDER SYSTEM - QUICK REFERENCE

## 🔑 KEY CONCEPTS AT A GLANCE

### 1️⃣ EDITABLE TEMPLATES
```
Admin Panel
    ↓
Message Templates Page
    ↓
Edit Template Content (with {placeholders})
    ↓
Save to Database
    ↓
Next sends use new template
```

**Example:**
```
OLD: "Hi, remember your appointment"
NEW: "Hi {patient_name}, reminder of {service_type} on {appointment_date}"
```

---

### 2️⃣ MANUAL REMINDERS
```
Doctor/Midwife sees Appointments List
    ↓
Sees yellow banner: "Click Reminder SMS or Email to send"
    ↓
Clicks blue "Reminder SMS" button
    ↓
System renders template + sends SMS
    ↓
Dispatch log created (audit trail)
    ↓
Confirmation: "Reminder SMS sent successfully"
```

---

### 3️⃣ AUTO REMINDERS (Optional - Admin Enables)
```
Scheduler runs EVERY HOUR
    ↓
Finds no-show appointments
    ↓
Checks safeguards:
   ✓ SMS enabled?
   ✓ Under daily limit (100)?
   ✓ Not in quiet hours (9PM-6AM)?
   ✓ No duplicate sent today?
    ↓
If All Pass: Send SMS with template
If Any Fail: Skip and log reason
    ↓
Dispatch log created (audit trail)
```

---

### 4️⃣ AUDIT LOGGING
```
Every Send → Dispatch Log Entry Created
    
Fields Captured:
├─ Who: Doctor/Midwife ID (sender_user_id)
├─ To Whom: Patient ID (patient_user_id)
├─ What: Appointment ID (appointment_id)
├─ Channel: SMS or Email
├─ Mode: Manual or Auto
├─ Status: Sent / Failed / Skipped
├─ Why (if skipped): Reason field
└─ When: Timestamp

Result: Complete traceability ✅
```

---

## 📱 USER INTERFACE TOUR

### Admin Dashboard
```
┌─ Sidebar
│  ├─ Message Templates ← Click here to edit templates
│  └─ SMS Management
│     └─ Settings/Toggles
│        ├─ Enable SMS
│        ├─ SMS Appointment Reminders
│        ├─ SMS Defaulter Recall
│        └─ Auto First Defaulter ← Enable for hourly auto-sends
└─
```

### Message Templates Page
```
┌─────────────────────────────────────┐
│ Message Templates                   │
├─────────────────────────────────────┤
│ ┌─ Appointment Reminder SMS       │ │
│ │ Subject: (N/A for SMS)          │ │
│ │ Content: E-Barangay: Reminder.. │ │
│ │ [Edit] [Delete]                 │ │
│ └─────────────────────────────────┘ │
│                                      │
│ ┌─ Appointment Reminder Email      │ │
│ │ Subject: Reminder: {service...  │ │
│ │ Content: Dear {patient_name}... │ │
│ │ [Edit] [Delete]                 │ │
│ └─────────────────────────────────┘ │
│                                      │
│ ┌─ Defaulter Recall SMS            │ │
│ │ [Edit] [Delete]                 │ │
│ └─────────────────────────────────┘ │
│                                      │
│ ┌─ Defaulter Recall Email          │ │
│ │ [Edit] [Delete]                 │ │
│ └─────────────────────────────────┘ │
└─────────────────────────────────────┘
```

### Appointments List (Doctor View)
```
┌─────────────────────────────────────────────────────┐
│ Appointments                                         │
├─────────────────────────────────────────────────────┤
│⚠️  Manual reminders: click Reminder SMS or           │
│    Reminder Email in each row to send.              │
├─────────────────────────────────────────────────────┤
│ Patient    │ Service  │ Date       │ Status │ Action│
├─────────────────────────────────────────────────────┤
│ Maria Cruz │ Prenatal │ Mar 09 8am │Pending│SMS📱  │
│            │ Care     │            │       │EMAIL📧│
├─────────────────────────────────────────────────────┤
│ John Doe   │ Vaccine  │ Mar 10...  │Approved...    │
├─────────────────────────────────────────────────────┤
│ Jane Smith │ Checkup  │ Mar 11...  │Rejected(no btn)
└─────────────────────────────────────────────────────┘

Buttons appear only for: pending, approved, rescheduled
Buttons hidden for: rejected, cancelled, archived, past appointments
```

---

## 📊 DATABASE SCHEMA (SIMPLIFIED)

### message_templates table
```
ID  │ template_key               │ type  │ subject           │ content
──────────────────────────────────────────────────────────────────────
1   │ defaulter_recall_email     │ email │ E-Barangay F...   │ [content]
2   │ defaulter_recall_sms       │ sms   │ NULL              │ [content]
3   │ appointment_reminder_email │ email │ Reminder: {...    │ [content]
4   │ appointment_reminder_sms   │ sms   │ NULL              │ [content]
```

### message_dispatch_logs table
```
ID│Appt │Patient│Sender│Template│Category        │Channel│Mode  │Status
──┼─────┼───────┼──────┼────────┼────────────────┼───────┼───────┼─────
1 │ 1   │ 3     │ 5    │ 4      │appointment_rem │sms    │manual │sent
2 │ 2   │ 7     │ 5    │ 3      │appointment_rem │email  │manual │sent
3 │ 5   │ 12    │ NULL │ 2      │defaulter_rec   │sms    │auto   │sent
4 │ 1   │ 3     │ 5    │ 4      │appointment_rem │sms    │manual │skipped
```

---

## 🔑 TEMPLATE PLACEHOLDERS REFERENCE

```
When you write a template, use these variables:

{patient_name}
└─ Example: "Maria Santos Cruz"

{service_type}
└─ Example: "Prenatal Care", "Vaccination", "Checkup"

{appointment_date}
└─ Example: "April 15, 2026"

{appointment_time}
└─ Example: "10:00 AM - 10:30 AM"

{doctor_name}
└─ Example: "Dr. Juan Dela Cruz"

{clinic_location}
└─ Example: "Barangay Health Center"
```

### Example Templates

**Template 1: Appointment Reminder (SMS)**
```
E-Barangay: Reminder of {service_type} appointment 
for {patient_name} on {appointment_date} at {appointment_time}. 
Call us if you need to reschedule.

Character count: ~138 characters
SMS Segments: 1 segment
```

**Template 2: Appointment Reminder (Email)**
```
Subject: Reminder: {service_type} Appointment - {appointment_date}

Body:
Dear {patient_name},

This is a friendly reminder about your {service_type} appointment.

Date & Time: {appointment_date} at {appointment_time}
Location: {clinic_location}

If you need to reschedule, please contact us.

Best regards,
E-Barangay Health
```

**Template 3: Defaulter Recall (SMS)**
```
Hi {patient_name}, we noticed you missed your {service_type} 
appointment on {appointment_date}. 
Please schedule a new appointment at your earliest convenience.
```

---

## ⚙️ CONFIGURATION QUICK REFERENCE

### File: config/messaging.php
```php
// Adjust these to change behavior
'auto_defaulter' => [
    'daily_limit' => 100,              // Max 100 auto-sends per day
    'quiet_hours_start' => '21:00',    // Don't send after 9 PM
    'quiet_hours_end' => '06:00',      // Don't send before 6 AM
],
```

### File: .env (SMS Gateway)
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=your-email@domain.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=ssl
```

---

## 🎯 WORKFLOWS AT A GLANCE

### Workflow 1: Admin Edits Template
```
1. Admin clicks "Message Templates"
2. Clicks "Edit" on a template
3. Changes content/subject
4. Clicks "Update"
5. ✅ New template saved
6. Next reminder sends use new template
```

### Workflow 2: Doctor Sends Manual Reminder
```
1. Doctor shows appointments list
2. Sees banner explaining manual workflow
3. Doctor clicks "Reminder SMS" button
4. System sends SMS using current template
5. ✅ Confirmation message appears
6. Log entry created in dispatch_logs
```

### Workflow 3: Hourly Auto Reminder (Optional)
```
1. Scheduler runs (every hour)
2. Checks: SMS enabled? Limit OK? Quiet hours? Duplicate?
3. If all pass: Send SMS to no-show patients
4. If any fail: Skip and log reason
5. ✅ Dispatch log entry created
6. Admin can review in logs
```

### Workflow 4: View Audit Trail
```
1. Admin/Staff wants to see reminders sent
2. Query dispatch_logs table:
   SELECT * FROM message_dispatch_logs 
   WHERE appointment_id = 1
3. ✅ See all sends: date, channel, status, reason
4. Complete traceability for compliance
```

---

## ✅ CHECKLIST: You Can Now...

- [x] Edit SMS and email templates from admin panel
- [x] Use placeholders like {patient_name}, {appointment_date}
- [x] See live SMS character count while editing
- [x] Send manual reminders by clicking buttons
- [x] Staff knows reminders are manual (yellow banner)
- [x] Every reminder is logged for audit trail
- [x] Filter logs by status, channel, mode
- [x] Enable auto first reminder (hourly)
- [x] Set daily limits and quiet hours
- [x] Verify all sends in dispatch logs

---

## 🚀 DEPLOYMENT QUICK STEPS

```bash
# 1. SSH to live server
ssh -p 65002 u174002700@145.79.25.197

# 2. Navigate
cd domains/ebarangayhealth.online/public_html

# 3. Pull code
git pull origin main

# 4. Run migration (creates dispatch logs table)
php artisan migrate --force

# 5. Seed templates
php artisan db:seed --class=MessageTemplateSeeder --force

# 6. Clear caches
php artisan optimize:clear

# 7. Verify
php artisan tinker
>>> App\Models\MessageTemplate::count()
4
>>> exit

# DONE! ✅ System live
```

---

## 🆘 TROUBLESHOOTING QUICK GUIDE

| Problem | Check This | Fix |
|---------|-----------|-----|
| Templates blank | DB migrated? | `php artisan migrate --force` |
| Buttons not showing | Appointment active? | Only pending/approved/rescheduled show buttons |
| SMS not sending | SMS enabled? | Admin → SMS Management → Enable SMS |
| SMS not sending | Balance? | Check PhilSMS account balance |
| SMS not sending | Quiet hours? | Auto blocked 9 PM - 6 AM (manual still works) |
| Can't edit template | Admin access? | Need admin role to access templates |
| Logs not appearing | Sent anything? | Logs appear after first send |

---

## 📞 SUPPORT REFERENCE

**Key Files:**
```
app/Services/TemplateService.php       (Renders templates)
app/Services/ReminderPolicyService.php (Checks safeguards)
app/Models/MessageDispatchLog.php       (Audit logs)
database/migrations/*dispatch_logs*     (Schema)
resources/views/admin/message-templates (Template UI)
resources/views/*/appointments/index    (Manual buttons)
```

**Commands:**
```
# Test template rendering
php artisan tinker
>>> App\Services\TemplateService::render('appointment_reminder_sms', $appt)

# View recent logs
>>> App\Models\MessageDispatchLog::latest()->limit(10)->get()

# Check policy
>>> (new App\Services\ReminderPolicyService())->canAutoSend($appt, 'sms', 1)
```

---

**System:** ✅ Ready to Deploy
**Coverage:** ✅ Manual & Auto Reminders
**Audit:** ✅ Complete Logging
**Safety:** ✅ All Safeguards Active

**Generated:** April 12, 2026
