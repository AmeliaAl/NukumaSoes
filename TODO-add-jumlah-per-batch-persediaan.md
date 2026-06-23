# TODO: Add Jumlah Per Batch Form to Persediaan Create

## Steps:
- [x] Step 1: Add jumlah_per_batch input field to resources/views/persediaan/create.blade.php after jumlah_masuk
- [x] Step 2: Add 'jumlah_per_batch' to $fillable in app/Models/PersediaanEntry.php
- [x] Step 3: Update app/Http/Controllers/PersediaanController.php store() to validate/handle jumlah_per_batch
- [x] Step 4: Check DB column (run migration if needed), test, complete

Current: Starting Step 1
