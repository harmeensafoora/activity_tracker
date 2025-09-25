<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Academic Tracker - Student</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
      background: #f7f9fc;
      color: #333;
    }

    header {
      background: #2c3e50;
      color: white;
      padding: 20px;
      text-align: center;
      position: relative;
    }

    header h1 {
      margin: 0;
      font-size: 2rem;
    }

    nav {
      position: absolute;
      top: 20px;
      right: 20px;
    }

    nav .btn {
      background: #e67e22;
      color: white;
      padding: 8px 15px;
      text-decoration: none;
      border-radius: 6px;
      font-weight: bold;
      transition: 0.3s;
    }

    nav .btn:hover {
      background: #305db1ff;
      color: white;
    }

    main {
      display: flex;
      justify-content: center;
      align-items: flex-start;
      gap: 30px;
      padding: 40px;
      flex-wrap: wrap;
    }

    .form-section {
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0px 4px 15px rgba(0,0,0,0.1);
      width: 350px;
      transition: transform 0.3s ease;
    }

    .form-section h2 {
      margin-bottom: 20px;
      font-size: 1.4rem;
      text-align: center;
      color: #2c3e50;
    }

    label {
      font-weight: bold;
      display: block;
      margin-bottom: 6px;
      margin-top: 12px;
    }

    input, select {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      margin-bottom: 10px;
      font-size: 14px;
    }

    .btn {
      background: #2980b9;
      color: white;
      border: none;
      padding: 10px;
      width: 100%;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
      transition: 0.3s;
    }

    .btn:hover {
      background: #1c5985;
    }
  </style>
</head>
<body>
  <header>
    <h1>🎓 Academic Tracker</h1>
    <nav>
      <a href="admin_login.php" class="btn">Admin Login</a>
    </nav>
  </header>

  <main>
    <section class="form-section">
      <h2>📝 Submit Seminar Topic</h2>
      <form method="POST" action="submit_seminar.php">
        <label>Name:</label>
        <input type="text" name="name" required>

        <label>Department:</label>
        <select name="department" id="department-select-submit" required>
          <option value="">-- Select Department --</option>
          <option value="Computer Science">Computer Science</option>
          <option value="Electronics & Communication">Electronics & Communication</option>
          <option value="Mechanical">Mechanical</option>
          <option value="Civil">Civil</option>
          <option value="Aeronautical">Aeronautical</option>
        </select>

        <label>Semester:</label>
        <select name="semester" id="semester-select-submit" required>
          <option value="">-- Select Semester --</option>
          <option value="1">1st</option>
          <option value="2">2nd</option>
          <option value="3">3rd</option>
          <option value="4">4th</option>
          <option value="5">5th</option>
          <option value="6">6th</option>
          <option value="7">7th</option>
          <option value="8">8th</option>
        </select>

        <label>Subject:</label>
        <select name="subject" id="subject-select-submit" required>
          <option value="">-- Select Subject --</option>
        </select>

        <label>Seminar Topic:</label>
        <input type="text" name="topic" required>

        <button type="submit" class="btn">Submit</button>
      </form>
    </section>

    <section class="form-section">
      <h2>🔎 View Submitted Seminars</h2>
      <form method="GET" action="view_seminar.php">
        <label>Department:</label>
        <select name="department" id="department-select-view">
          <option value="">All</option>
          <option value="Computer Science">Computer Science</option>
          <option value="Electronics & Communication">Electronics & Communication</option>
          <option value="Mechanical">Mechanical</option>
          <option value="Civil">Civil</option>
          <option value="Aeronautical">Aeronautical</option>
        </select>

        <label>Semester:</label>
        <select name="semester" id="semester-select-view">
          <option value="">All</option>
          <option value="1">1st</option>
          <option value="2">2nd</option>
          <option value="3">3rd</option>
          <option value="4">4th</option>
          <option value="5">5th</option>
          <option value="6">6th</option>
          <option value="7">7th</option>
          <option value="8">8th</option>
        </select>

        <label>Subject:</label>
        <select name="subject" id="subject-select-view">
          <option value="">All</option>
        </select>

        <label>Search by Topic:</label>
        <input type="text" name="topic">

        <button type="submit" class="btn">View Seminars</button>
      </form>
    </section>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Logic for the Submit Seminar form's dynamic dropdowns
      const deptSelectSubmit = document.getElementById('department-select-submit');
      const semesterSelectSubmit = document.getElementById('semester-select-submit');
      const subjectSelectSubmit = document.getElementById('subject-select-submit');

      function fetchSubjectsSubmit() {
          const semesterId = semesterSelectSubmit.value;
          const deptName = deptSelectSubmit.value;
          subjectSelectSubmit.innerHTML = '<option value="">Loading...</option>';

          if (semesterId && deptName) {
              fetch(`fetch_subjects.php?semester_id=${semesterId}&dept_name=${encodeURIComponent(deptName)}`)
                  .then(response => {
                      if (!response.ok) {
                          throw new Error('Network response was not ok');
                      }
                      return response.json();
                  })
                  .then(subjects => {
                      subjectSelectSubmit.innerHTML = '<option value="">-- Select Subject --</option>';
                      if (subjects.length > 0) {
                          subjects.forEach(subject => {
                              const option = document.createElement('option');
                              option.value = subject.id;
                              option.textContent = subject.name;
                              subjectSelectSubmit.appendChild(option);
                          });
                      } else {
                          subjectSelectSubmit.innerHTML = '<option value="">No subjects found</option>';
                      }
                  })
                  .catch(error => {
                      console.error('There has been a problem with your fetch operation:', error);
                      subjectSelectSubmit.innerHTML = '<option value="">Error loading subjects</option>';
                  });
          } else {
              subjectSelectSubmit.innerHTML = '<option value="">-- Select Subject --</option>';
          }
      }

      deptSelectSubmit.addEventListener('change', fetchSubjectsSubmit);
      semesterSelectSubmit.addEventListener('change', fetchSubjectsSubmit);

      // Logic for the View Seminar form's dynamic dropdowns
      const deptSelectView = document.getElementById('department-select-view');
      const semesterSelectView = document.getElementById('semester-select-view');
      const subjectSelectView = document.getElementById('subject-select-view');

      function fetchSubjectsView() {
          const semesterId = semesterSelectView.value;
          const deptName = deptSelectView.value;
          subjectSelectView.innerHTML = '<option value="">Loading...</option>';

          if (semesterId && deptName) {
              fetch(`fetch_subjects.php?semester_id=${semesterId}&dept_name=${encodeURIComponent(deptName)}`)
                  .then(response => {
                      if (!response.ok) {
                          throw new Error('Network response was not ok');
                      }
                      return response.json();
                  })
                  .then(subjects => {
                      subjectSelectView.innerHTML = '<option value="">All</option>';
                      if (subjects.length > 0) {
                          subjects.forEach(subject => {
                              const option = document.createElement('option');
                              option.value = subject.id;
                              option.textContent = subject.name;
                              subjectSelectView.appendChild(option);
                          });
                      } else {
                          subjectSelectView.innerHTML = '<option value="">No subjects found</option>';
                      }
                  })
                  .catch(error => {
                      console.error('There has been a problem with your fetch operation:', error);
                      subjectSelectView.innerHTML = '<option value="">Error loading subjects</option>';
                  });
          } else {
              subjectSelectView.innerHTML = '<option value="">All</option>';
          }
      }

      deptSelectView.addEventListener('change', fetchSubjectsView);
      semesterSelectView.addEventListener('change', fetchSubjectsView);

      // Display pop-up messages
      <?php
      if (isset($_SESSION['success_message'])) {
          echo 'alert("' . $_SESSION['success_message'] . '");';
          unset($_SESSION['success_message']);
      }
      if (isset($_SESSION['error_message'])) {
          echo 'alert("' . $_SESSION['error_message'] . '");';
          unset($_SESSION['error_message']);
      }
      ?>
    });
  </script>
</body>
</html>