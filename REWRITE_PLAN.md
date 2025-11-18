# CREDAI Portal Rewrite Plan

## Overview

Streamlined exhibitor registration system where admin manages companies and provides unique links for clients to complete their exhibitor details and project information.

---

## System Flow

### Phase 1: Admin Setup

1. **Admin adds companies** (using existing seed data)

    - Company name
    - Main contact person name
    - Registered phone number (10 digits)
    - Stall details (type, number, size)
    - Payment details (total, received, pending)

2. **System generates unique link** for each company
    - Format: `/register/{company_token}`
    - Token stored in `companies` table
    - Link is permanent and unique per company

### Phase 2: Client Registration

1. **Client receives unique link** from admin
2. **Client accesses form** via unique link
3. **Form displays locked company info** (view-only):

    - Company name
    - Contact person
    - Phone number
    - Stall details

4. **Client fills out remaining details**:

    - **Company Details**

        - Office address
        - City
        - GST number (optional)
        - PAN number (optional)
        - Email (optional)
        - Website (optional)

    - **Branding & Media**

        - Logo upload (PNG, JPG, PDF, CDR - max 5MB)
        - Brochure upload (PDF - max 10MB)
        - Video URL (optional)
        - Social media links (Facebook, LinkedIn, Instagram, YouTube)
        - Additional details (optional)

    - **Exhibition Display**

        - Facia name (required)
        - Momento name (optional)
        - Extra furniture details (optional)
        - Exhibitor passes details (optional)
        - Car pass details (optional)

    - **Projects** (Multiple projects allowed)
        - Project name
        - Area/Location
        - Category
        - Square footage
        - Budget range
        - Handover date
        - Status
        - PDF upload (optional)
        - Video URL (optional)
        - USP
        - Contact person
        - Logo upload (optional)

5. **Auto-save functionality**

    - Data saved periodically to `draft_exhibitors` table
    - Client can return via same link to continue
    - No separate "resume token" needed

6. **Final submission**
    - Creates record in `exhibitors` table
    - Creates related records in `projects` table
    - Marks draft as completed
    - Shows thank you page
    - **One submission only** - link becomes inactive after submission

### Phase 3: Admin Management

1. **View all submissions**
    - List of all exhibitors
    - Filter by company
    - View full details
    - See associated projects

---

## Database Schema Changes

### `companies` Table

**New Fields:**

-   `registration_token` (string, unique, nullable) - for unique URLs
-   `has_submitted` (boolean, default false) - track if form submitted
-   `submitted_at` (timestamp, nullable) - when form was submitted

**Existing Fields:** (keep as is)

-   company_name
-   registered_number
-   main_person_name
-   stall_type, stall_number, stall_size
-   total_payment, payment_received, payment_pending

### `exhibitors` Table

**Modified:**

-   `company_id` - required (not nullable)
-   Remove redundant fields that come from company:
    -   ~~brand_name~~ (use company.company_name)
    -   ~~phone_number~~ (use company.registered_number)
    -   ~~contact_person_name~~ (use company.main_person_name)
    -   ~~stall_type, stall_number, stall_size~~ (use from company)
    -   ~~total_payment, payment_received, payment_pending~~ (use from company)

**Keep:**

-   All other fields (office_address, city, gst_number, pan_number, email, website, logo_path, brochure_path, video_url, social_media_links, facia_name, additional_details, extra_furniture_details, exhibitor_passes_details, momento_name, car_pass_details)

### `draft_exhibitors` Table

**Modified:**

-   `company_id` - required (not nullable)
-   Remove `resume_token` field (not needed)
-   Keep auto-save functionality but tied to company_id
-   Remove redundant fields matching company table

### `projects` Table

**No changes needed** - already properly linked to exhibitors

---

## File Structure Changes

### Models

-   ✅ `Company.php` - add token generation, submission tracking
-   ✅ `Exhibitor.php` - update relationships, remove redundant fields
-   ✅ `DraftExhibitor.php` - update to work with company_id
-   ✅ `Project.php` - no changes needed

### Livewire Components

**Remove:**

-   ❌ `ExhibitorForm.php` (old parent class)
-   ❌ `PublicExhibitorForm.php` (old public form)
-   ❌ `ExhibitorsList.php` (if not used by admin)
-   ❌ `DraftExhibitorsList.php` (remove draft management)
-   ❌ `TestComponent.php` (cleanup)

**Keep & Update:**

-   ✅ `CompanyForm.php` - add token generation
-   ✅ `CreateCompany.php` - add token generation
-   ✅ `EditCompany.php` - show token/link
-   ✅ `CompaniesList.php` - show submission status

**Create New:**

-   ✨ `ClientRegistrationForm.php` - new streamlined form for clients
-   ✨ `ExhibitorDetails.php` - admin view of submitted exhibitor

### Views

**Remove old views:**

-   Remove `livewire/exhibitor-form.blade.php`
-   Remove `livewire/public-exhibitor-form.blade.php`

**Create new views:**

-   ✨ `livewire/client-registration-form.blade.php` - clean, step-based form
-   ✨ `livewire/exhibitor-details.blade.php` - admin detail view
-   ✨ `exhibitor/link-expired.blade.php` - shown when link already used

### Routes

**Update:**

```php
// Public route (no auth required)
Route::get('register/{token}', ClientRegistrationForm::class)->name('client.register');
Route::get('registration/complete', fn() => view('client.complete'))->name('client.complete');
Route::get('registration/expired', fn() => view('client.expired'))->name('client.expired');

// Admin routes (auth required)
Route::middleware(['auth'])->group(function () {
    Route::get('companies', CompaniesList::class)->name('companies.index');
    Route::get('companies/create', CreateCompany::class)->name('companies.create');
    Route::get('companies/{company}/edit', EditCompany::class)->name('companies.edit');

    Route::get('exhibitors', ExhibitorsList::class)->name('exhibitors.index');
    Route::get('exhibitors/{exhibitor}', ExhibitorDetails::class)->name('exhibitors.show');
});
```

---

## Implementation Steps

### Step 1: Database Updates

1. Create migration to add `registration_token`, `has_submitted`, `submitted_at` to companies table
2. Create migration to remove redundant fields from exhibitors table
3. Create migration to update draft_exhibitors table
4. Generate tokens for existing companies

### Step 2: Update Models

1. Update Company model - add token generation method
2. Update Exhibitor model - update fillable, remove redundant fields
3. Update DraftExhibitor model - update fillable

### Step 3: Create New Client Form

1. Create `ClientRegistrationForm` Livewire component
2. Implement auto-save to draft
3. Add step-by-step wizard UI (like current PublicExhibitorForm)
4. File upload handling (logo, brochure, project files)
5. Project management (add/remove multiple projects)
6. Validation for each step

### Step 4: Update Admin Components

1. Update CompanyForm to generate token on creation
2. Update CompaniesList to show submission status
3. Update EditCompany to display/copy registration link
4. Create ExhibitorDetails component to view submissions

### Step 5: Update Routes & Cleanup

1. Update web.php routes
2. Remove old components and views
3. Update navigation

### Step 6: Testing

1. Test complete registration flow
2. Test auto-save functionality
3. Test file uploads
4. Test project CRUD
5. Test one-submission-only enforcement
6. Test admin viewing submissions

---

## Key Features

### For Clients

-   ✅ Simple unique link access (no login required)
-   ✅ View company info (locked)
-   ✅ Multi-step form with progress indicator
-   ✅ Auto-save (return anytime via same link)
-   ✅ File uploads with preview
-   ✅ Add multiple projects
-   ✅ Clear validation messages
-   ✅ Thank you page after submission

### For Admin

-   ✅ Easy company management
-   ✅ Generate & copy registration links
-   ✅ View submission status
-   ✅ View all submitted exhibitor details
-   ✅ See all projects per exhibitor

### Technical

-   ✅ Clean database structure (no redundant data)
-   ✅ Proper relationships
-   ✅ Secure token-based access
-   ✅ One submission per company enforcement
-   ✅ File storage in organized folders
-   ✅ Follows Laravel best practices
-   ✅ Uses Livewire 3 & Flux UI
-   ✅ Pest tests for all features

---

## File Upload Structure

```
storage/app/public/
├── exhibitors/
│   ├── logos/
│   ├── brochures/
│   └── {company_token}/
│       └── projects/
│           ├── pdfs/
│           └── logos/
```

---

## Questions Answered

1. ✅ No temporary URLs - single permanent token per company
2. ✅ Company info locked and view-only
3. ✅ One submission per company only
4. ✅ Admin can view all submitted forms
5. ✅ No separate draft system - auto-save via company link
6. ✅ No authentication needed - just the link

---

## Next Steps

1. Review and approve this plan
2. Begin implementation following the steps above
3. Test thoroughly in development
4. Deploy to production

---

**Estimated Time:** 4-6 hours for complete implementation
**Complexity:** Medium
**Breaking Changes:** Yes - complete rewrite of exhibitor flow
