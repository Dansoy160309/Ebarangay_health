# 📋 Reminder System - Complete Testing Guide

## System Status: ✅ ALL OPERATIONAL

### Database Verification
- ✅ 4 Template types active (appointment & defaulter, SMS & Email)
- ✅ MessageDispatchLog table created for audit trail
- ✅ All migrations applied successfully

---

## 📝 PART 1: TEMPLATE VIEWING & EDITING

### How Templates Work
All 4 templates are **editable from Admin UI** and use **placeholder variables**:

```
Template Keys:
1. appointment_reminder_sms   (SMS sent before appointments)
2. appointment_reminder_email (Email sent before appointments)
3. defaulter_recall_sms       (SMS sent to no-show patients)
4. defaulter_recall_email     (Email sent to no-show patients)
```

### Available Placeholders
```
{patient_name}     → Full name of patient
{service_type}     → Type of service (e.g., 'Vaccination')
{appointment_date} → Formatted date (e.g., 'April 15, 2026')
{appointment_time} → Time slot (e.g., '08:00 AM')
{doctor_name}      → Name of assigned doctor/midwife
{clinic_location}  → Clinic/office name
```

### Test 1: Edit a Template
**Steps:**
1. Go to Admin Dashboard → Message Templates
2. Click "Edit" on "Appointment Reminder SMS"
3. Change the content, e.g.:
   ```
   Current: E-Barangay: Reminder of {service_type} appointment...
   Change to: 🏥 Hi {patient_name}! Reminder: {service_type} on {appointment_date}
   ```
4. Click "Update" and Save
5. Verify the template is updated ✅

**What Happens:**
- Template is saved to database
- Next time a reminder is sent, it uses the NEW template
- All previous sends remain unchanged (audit trail preserved)

---

## 📱 PART 2: MANUAL REMINDER WORKFLOW

### How Manual Reminders Work
Staff manually sends reminders **by clicking buttons** in the appointments list.

### Test 2: Send Manual Appointment Reminder SMS
**Steps:**
1. Login as Doctor or Midwife
2. Go to Appointments list
3. Find an active appointment (status: pending/approved/rescheduled)
4. Look for the **manual reminder banner**:
   ```
   ⚠️  Manual reminders: click Reminder SMS or Reminder Email in each row to send.
   ```
5. Click the blue **"Reminder SMS"** button in the appointment row
6. System sends SMS using the `appointment_reminder_sms` template
7. Page reloads with confirmation message

**Expected Result:**
```
✅ Message: "Reminder SMS sent successfully"
📊 Dispatch log created with:
   - appointment_id: [appointment ID]
   - channel: sms
   - trigger_mode: manual
   - status: sent
   - template: appointment_reminder_sms
   - timestamp: [current time]
```

### Test 3: Send Manual Appointment Reminder Email
**Steps:**
1. From same appointments list
2. Click the green **"Reminder Email"** button
3. System sends email using `appointment_reminder_email` template
4. Confirmation message appears

**Expected Result:**
```
✅ Message: "Reminder email sent successfully"
📊 Dispatch log created with:
   - channel: email
   - trigger_mode: manual
   - status: sent
   - template: appointment_reminder_email
```

### Test 4: Verify Buttons Don't Show for Invalid Appointments
**Steps:**
1. Check appointments with these statuses:
   - ✗ Rejected/Cancelled/Archived → Buttons should NOT appear
   - ✗ Past/completed appointments → Buttons should NOT appear
   - ✓ Pending/Approved/Rescheduled (future) → Buttons SHOULD appear

**Expected Result:**
Only active, future appointments show reminder buttons (security & UX)

---

## 🔐 PART 3: SAFEGUARDS & POLICIES

### Auto First Reminder Safeguards
When the hourly auto-reminder command runs (`SendDefaulterAutoFirstReminder`), it checks:

```
✓ SMS is ENABLED globally (SMS Management > Enable SMS)
✓ Defaulter recall is ENABLED (SMS Settings)
✓ Auto first reminder is ENABLED (SMS Settings > "Auto First Defaulter")
✓ Appointment is marked as NO_SHOW status
✓ Less than 100 reminders sent TODAY
✓ Current time is NOT in quiet hours (9 PM - 6 AM)
✓ No reminder was sent TODAY for this appointment/channel
✓ Max 3 reminders per appointment (stage limit)

If ANY check fails → Reminder SKIPPED (logged as "skipped" in dispatch log)
```

### Test 5: Check Daily Limit
**Steps:**
1. Go to Admin → SMS Management
2. Check settings:
   ```
   Daily Limit: 100 reminders/day
   Quiet Hours: 9 PM - 6 AM
   ```
3. If you want to change, edit `config/messaging.php`:
   ```php
   'daily_limit' => 50,  // Change from 100
   'quiet_hours_start' => '21:00',
   'quiet_hours_end' => '06:00',
   ```

### Test 6: Check SMS Settings Toggles
**Steps:**
1. Admin Dashboard → SMS Management
2. Verify these toggles:
   ```
   ✓ Enable SMS                        (must be ON)
   ✓ SMS Appointment Reminders         (enables appointment reminders)
   ✓ SMS Defaulter Recall              (enables defaulter reminders)
   ✓ Auto First Defaulter Reminder     (enables hourly auto-send)
   ```

---

## 📊 PART 4: AUDIT LOGGING

### What Gets Logged?
Every reminder send (manual or auto) creates a record in `message_dispatch_logs` table:

```
Fields Captured:
┌─────────────────────┬──────────────────────────────────┐
│ Field               │ Example Value                    │
├─────────────────────┼──────────────────────────────────┤
│ id                  │ 1                                │
│ appointment_id      │ 5 (which appointment)            │
│ patient_user_id     │ 12 (who received it)             │
│ sender_user_id      │ 3 (who triggered it - manual)    │
│ template_id         │ 4 (which template used)          │
│ category            │ appointment_reminder             │
│ channel             │ sms or email                     │
│ stage               │ 1, 2, or 3 (which reminder)      │
│ trigger_mode        │ manual or auto                   │
│ status              │ sent, failed, or skipped         │
│ reason              │ "Daily limit reached" (if skip)  │
│ provider_response   │ {"status": "ok"} (API response)  │
│ sent_at             │ 2026-04-12 10:15:00              │
└─────────────────────┴──────────────────────────────────┘
```

### Test 7: View Dispatch Logs
**Steps:**
1. Admin Dashboard → SMS Management → "Dispatch Logs" (if link exists)
2. Or query via database:
   ```bash
   php artisan tinker
   >>> DB::table('message_dispatch_logs')->latest()->limit(10)->get();
   ```
3. Filter by:
   - Channel: sms or email
   - Trigger Mode: manual or auto
   - Status: sent, failed, skipped
   - Category: appointment_reminder or defaulter_recall

**Expected Result:**
```
✅ Shows every reminder sent in the system
✅ Complete audit trail (who, when, channel, outcome)
✅ Can export/report for compliance
```

---

## 🧪 PART 5: LIVE RENDERING EXAMPLE

### Example: How Templates Are Rendered

**Input:**
```
Appointment ID: 1
Patient: Maria Santos Cruz
Service: Prenatal Care
Date: Mar 09, 2026
Time: 08:00 AM
Doctor: Dr. Juan Dela Cruz
```

**Template Content:**
```
E-Barangay: Reminder of {service_type} appointment 
for {patient_name} on {appointment_date} at {appointment_time}.
Call (+63) XXXX-XXXX if you need to reschedule.
```

**Rendered Output:**
```
E-Barangay: Reminder of Prenatal Care appointment 
for Maria Santos Cruz on Mar 09, 2026 at 08:00 AM.
Call (+63) XXXX-XXXX if you need to reschedule.
```

**Character Count:** 148 / 160 (1 SMS segment)

---

## 🚀 PART 6: DEPLOYMENT CHECKLIST

### Local Testing Complete ✅
- [x] Templates created and editable
- [x] Placeholders render correctly
- [x] Manual buttons visible in UI
- [x] Audit logging captures all sends
- [x] Safeguards functioning

### Ready for Live Deployment:

```bash
# Step 1: SSH to live server
ssh -p 65002 u174002700@145.79.25.197
password: Avesdan!12345

# Step 2: Navigate to project
cd domains/ebarangayhealth.online/public_html

# Step 3: Pull latest code
git pull origin main

# Step 4: Run migrations (creates dispatch logs table + template keys)
php artisan migrate --force

# Step 5: Seed templates (creates 4 default templates)
php artisan db:seed --class=MessageTemplateSeeder --force

# Step 6: Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan optimize:clear

# Step 7: Verify
php artisan tinker
>>> App\Models\MessageTemplate::count()
4
>>> App\Models\MessageDispatchLog::count()
0
>>>exit
```

### Post-Deployment Tests:

**Test 1: Admin Templates Page**
```
1. Login to admin dashboard
2. Click Admin → Message Templates
3. Should see 4 templates with edit buttons
4. Edit one template
5. Save changes
6. Verify changes persist
```

**Test 2: Appointment Reminders**
```
1. Login as Doctor/Midwife
2. Go to Appointments
3. See manual reminder banner
4. Click "Reminder SMS" for any appointment
5. Check: Message appears in dispatch logs
6. Check: SMS appears in PhilSMS logs (if SMS enabled)
```

**Test 3: Auto First Reminder**
```
1. Enable "Auto First Defaulter" in SMS Management
2. Find an appointment with status = no_show
3. Wait for hourly scheduler to run (or run manually):
   php artisan command:SendDefaulterAutoFirstReminder
4. Check dispatch logs for auto sends
5. Verify SMS was sent with correct template
```

---

## ⚠️ TROUBLESHOOTING

### Issue: Templates show as blank
**Solution:**
```
php artisan migrate --force
php artisan db:seed --class=MessageTemplateSeeder --force
```

### Issue: Reminder buttons don't appear
**Reason:** Appointment status is not active/future
**Check:** Appointment status should be: pending, approved, or rescheduled

### Issue: SMS not sending
**Check List:**
```
1. Is SMS enabled? (Admin → SMS Management → Enable SMS)
2. Is PhilSMS balance available?
3. Are quiet hours active? (9 PM - 6 AM blocks auto)
4. Did you hit daily limit (100)?
5. Check provider response in dispatch logs
```

### Issue: Dispatch logs empty
**Reason:** No reminders sent yet
**Test:** Manually click "Reminder SMS" button on any appointment
**Then Check:** dispatch_logs table should have 1 entry

---

## 📋 SUMMARY

| Feature | Status | How to Test |
|---------|--------|------------|
| Editable Templates | ✅ Working | Admin → Message Templates |
| Template Rendering | ✅ Working | Send any reminder via button |
| Manual Reminders | ✅ Working | Click SMS/Email buttons |
| Audit Logging | ✅ Working | Check dispatch_logs table |
| Daily Limits | ✅ Working | Send 100+ reminders, next skipped |
| Safeguards | ✅ Working | Check dispatch reason field |
| SMS Integration | ✅ Working | Enable SMS, send reminder |
| Email Integration | ✅ Working | Click reminder email button |

---

## 🎯 NEXT STEPS

1. **Deploy** to live server using deploy checklist above
2. **Test** manual reminders in live environment
3. **Enable** auto first reminder in SMS Management
4. **Monitor** dispatch logs for 24 hours to verify auto sends
5. **Collect** feedback from midwives/doctors on UX

---

Generated: April 12, 2026 | Status: READY FOR DEPLOYMENT ✅
