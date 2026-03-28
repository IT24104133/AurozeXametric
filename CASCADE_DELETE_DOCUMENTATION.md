# Cascade Delete Implementation for Past Papers

## Overview
This document describes the cascade delete implementation for the Past Paper system, ensuring data integrity and automatic question bank updates.

## Database Foreign Keys (Already Configured)

### 1. Questions Table
**Migration**: `2026_02_05_130002_create_past_paper_questions_table.php`
```php
$table->foreignId('past_paper_id')->constrained('past_papers')->cascadeOnDelete();
```
- When a `past_papers` record is deleted, all related `past_paper_questions` are automatically deleted

### 2. Options Table
**Migration**: `2026_02_05_130003_create_past_paper_options_table.php`
```php
$table->foreignId('question_id')->constrained('past_paper_questions')->cascadeOnDelete();
```
- When a `past_paper_questions` record is deleted, all related `past_paper_options` are automatically deleted

### 3. Attempts Table
**Migration**: `2026_02_05_130004_create_past_paper_attempts_table.php`
```php
$table->foreignId('past_paper_id')->constrained('past_papers')->cascadeOnDelete();
```
- When a `past_papers` record is deleted, all related `past_paper_attempts` are automatically deleted

## Controller Implementation

### AdminPastPaperController@destroy
**File**: `app/Http/Controllers/Admin/AdminPastPaperController.php`

**Logic Flow**:
1. Validate stream and paper ownership
2. **For edu_department papers**:
   - Iterate through all questions and options
   - Delete stored images from `storage/app/public/`:
     - Question images (`question_image`)
     - Option images (`option_image`)
3. **For free_style papers**:
   - No images to delete (configuration only)
4. Delete the paper record
   - Database CASCADE automatically deletes:
     - All questions
     - All options (via question cascade)
     - All attempts
5. Redirect with success message

**Success Messages**:
- edu_department: "Past paper deleted successfully. Question bank updated automatically."
- free_style: "Free Style paper configuration deleted successfully."

## Free Style Question Bank Behavior

### Automatic Update
**File**: `app/Http/Controllers/Student/StudentPastPaperAttemptController.php`
**Method**: `generateFreeStyleQuestionOrder()`

**Query Logic**:
```php
$pool = PastPaperQuestion::query()
    ->whereHas('pastPaper', function ($q) use ($paper) {
        $q->where('subject_id', $paper->subject_id)
            ->where('stream', $paper->stream)
            ->where('category', 'edu_department')
            ->where('status', 'published');
    })
    ->get();
```

**Key Points**:
- Queries ALL questions from ALL published edu_department papers
- Filters by subject_id and stream
- When an edu_department paper is deleted:
  - Its questions are removed from the database (cascade)
  - Next free_style attempt automatically uses remaining questions
  - No manual refresh or paper recreation needed

**Example Scenario**:
1. Subject "Mathematics" has 3 edu_department papers:
   - 2019 (30 questions)
   - 2020 (35 questions)
   - 2021 (40 questions)
   - **Total pool: 105 questions**

2. Admin deletes 2019 paper:
   - 30 questions removed from database
   - **New pool: 75 questions** (from 2020 + 2021)

3. Student starts free_style attempt:
   - Automatically uses 75 remaining questions
   - No intervention required

## Image Storage Cleanup

### Storage Path
All images stored in: `storage/app/public/`

### Cleanup Process
1. Check if file exists: `Storage::disk('public')->exists($path)`
2. Delete file: `Storage::disk('public')->delete($path)`

### Image Fields
- `past_paper_questions.question_image`
- `past_paper_options.option_image`

## Testing Checklist

### 1. Delete edu_department Paper
- [ ] Paper deleted from database
- [ ] Questions deleted (cascade)
- [ ] Options deleted (cascade)
- [ ] Attempts deleted (cascade)
- [ ] Question images deleted from storage
- [ ] Option images deleted from storage
- [ ] Success message shown
- [ ] Redirected to papers list

### 2. Delete free_style Paper
- [ ] Paper deleted from database
- [ ] No questions to delete (free_style has no direct questions)
- [ ] Attempts deleted (cascade)
- [ ] Success message shown
- [ ] Redirected to papers list

### 3. Free Style Question Bank Update
- [ ] Start free_style attempt before deletion
- [ ] Note total questions available
- [ ] Delete an edu_department paper
- [ ] Start new free_style attempt
- [ ] Verify question count reduced by deleted paper's count
- [ ] Verify no questions from deleted paper appear

### 4. Insufficient Questions Scenario
- [ ] Delete edu_department papers until pool is too small
- [ ] Try to start free_style attempt
- [ ] Verify error message: "Insufficient questions in bank"
- [ ] Verify redirected back to papers list

## Error Handling

### Insufficient Questions
If deleted papers reduce the bank below required count for free_style configuration:
- Method `generateFreeStyleQuestionOrder()` returns empty array
- Student sees: "Insufficient questions in the bank to generate this exam"
- Redirected to subject papers list

### Solution
Admin must either:
1. Add more edu_department papers with questions
2. Reduce free_style paper configuration (total_questions, count_s, count_m, count_h)

## Security Considerations

1. **Stream Validation**: Ensures paper belongs to requested stream
2. **Paper Ownership**: Validates paper exists and belongs to correct stream
3. **File Existence Check**: Prevents errors when deleting non-existent images
4. **Cascade Delete**: Prevents orphaned records in database

## Performance Notes

- Image deletion happens before paper deletion
- Large papers with many images may take longer to delete
- Consider adding progress indicator for papers with 100+ questions
- Database cascade is efficient (single transaction)

## Maintenance

### Future Enhancements
1. Soft delete option (archive instead of permanent delete)
2. Bulk delete with progress bar
3. Undo delete within grace period
4. Export paper before delete (backup)

### Monitoring
- Log paper deletions with admin user ID
- Track question bank size per subject
- Alert when bank falls below threshold for free_style

---

**Last Updated**: February 7, 2026
**Status**: ✅ Implemented and Ready for Testing
