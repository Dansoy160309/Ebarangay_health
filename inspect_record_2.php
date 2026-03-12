$r = \App\Models\HealthRecord::find(2);
if ($r) {
    echo "ID: " . $r->id . "\n";
    echo "Raw (getAttributes): " . var_export($r->getAttributes()['immunizations'], true) . "\n";
    echo "Casted (access): " . var_export($r->immunizations, true) . "\n";
    echo "Type of Casted: " . gettype($r->immunizations) . "\n";
} else {
    echo "Record 2 not found.\n";
}
