document.addEventListener('DOMContentLoaded', function(){
  const dept = document.getElementById('deptSelect');
  const sem = document.getElementById('semSelect');
  const subject = document.getElementById('subjectSelect');

  function loadSubjects(){
    const d = dept.value, s = sem.value;
    subject.innerHTML = '<option>Loading...</option>';
    if (!d || !s){ subject.innerHTML = '<option>-- Choose Department & Semester first --</option>'; return; }
    fetch('fetch_subjects.php?dept_id=' + encodeURIComponent(d) + '&semester_id=' + encodeURIComponent(s))
      .then(r=>r.json())
      .then(data=>{
        subject.innerHTML = '';
        if (data.length === 0){ subject.innerHTML = '<option value="">No subjects found</option>'; return; }
        data.forEach(x=>{
          const opt = document.createElement('option');
          opt.value = x.id; opt.textContent = x.name;
          subject.appendChild(opt);
        });
      })
      .catch(err=>{
        subject.innerHTML = '<option value="">Error loading</option>';
      });
  }

  dept.addEventListener('change', loadSubjects);
  sem.addEventListener('change', loadSubjects);
});