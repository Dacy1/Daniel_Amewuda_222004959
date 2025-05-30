<?php
require_once 'config.php';

try {
    $pdo = getConnection();
    
    // Get about information
    $about = $pdo->query("SELECT * FROM about_info LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    
    // Get projects
    $projects = $pdo->query("SELECT * FROM projects WHERE status = 'active' ORDER BY date_completed DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);
    
    // Get skills
    $skills = $pdo->query("SELECT * FROM skills WHERE status = 'active' ORDER BY skill_name")->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // Fallback to static data if database is not available
    $about = [
        'name' => 'Daniel Amewuda',
        'title' => 'Development Specialist | Researcher | Project Manager',
        'description' => 'As an impact-oriented development specialist focused on the digital economy, education, and skills development, I am passionate about using my skills and expertise to drive positive change in society.',
        'email' => 'ame23dacy@gmail.com',
        'phone' => '+233 240858453',
        'address' => 'House number 5 Aplite close, West Legon, Accra, Ghana'
    ];
    $projects = [];
    $skills = [];
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta
      name="description"
      content="<?php echo htmlspecialchars($about['name'] ?? 'Daniel Amewuda'); ?> - Development Specialist focusing on digital economy, education, and skills development"
    />
    <title><?php echo htmlspecialchars($about['name'] ?? 'Daniel Amewuda'); ?> - Development Specialist</title>
    <link rel="stylesheet" href="styles.css" />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />
  </head>
  <body>
    <!-- Navigation -->
    <nav class="navbar">
      <div class="container">
        <a href="#home" class="logo"><?php echo htmlspecialchars($about['name'] ?? 'Daniel Amewuda'); ?></a>
        <ul class="nav-links">
          <li><a href="#home" class="active">Home</a></li>
          <li><a href="#about">About</a></li>
          <li><a href="#experience">Experience</a></li>
          <li><a href="#projects">Projects</a></li>
          <li><a href="#education">Education</a></li>
          <li><a href="#contact">Contact</a></li>
          <li><a href="admin/login.php" style="color: #fcd116;">Admin Login</a></li>
        </ul>
        <button class="hamburger">☰</button>
      </div>
    </nav>

    <!-- Hero Section -->
    <section id="home">
      <div class="container">
        <div class="hero-content">
          <h1><?php echo htmlspecialchars($about['name'] ?? 'Daniel Amewuda'); ?></h1>
          <p><?php echo htmlspecialchars($about['title'] ?? 'Development Specialist | Researcher | Project Manager'); ?></p>
          <div class="hero-buttons">
            <a href="#contact" class="btn">Get In Touch</a>
            <a href="#experience" class="btn btn-outline">View Experience</a>
          </div>
        </div>
      </div>
    </section>

    <!-- About Section -->
    <section id="about">
      <div class="container">
        <h2>About Me</h2>
        <div class="about-content">
          <div class="about-img">
            <img src="images/Danielamewuda.jpg" alt="<?php echo htmlspecialchars($about['name'] ?? 'Daniel Amewuda'); ?>" />
          </div>
          <div class="about-text">
            <?php if (!empty($about['description'])): ?>
              <p><?php echo nl2br(htmlspecialchars($about['description'])); ?></p>
            <?php else: ?>
              <p>
                As an impact-oriented development specialist focused on the
                digital economy, education, and skills development, I am
                passionate about using my skills and expertise to drive positive
                change in society.
              </p>
              <p>
                With a strong background and decade's experience in programme
                implementation, social research and development consulting, I have
                a proven track record of leadership, project management and
                conducting innovative research to addresses pressing issues.
              </p>
              <p>
                My work is characterized by a commitment to rigor and precision,
                as well as a deep appreciation for the complexities of the
                challenges we face. Whether working independently or as part of a
                team, I am dedicated to producing actionable insights and
                solutions that can make meaningful difference in people's lives.
              </p>
            <?php endif; ?>

            <div class="skills">
              <h3>Core Competencies</h3>
              <div class="skills-grid">
                <?php if (!empty($skills)): ?>
                  <?php foreach ($skills as $skill): ?>
                    <div class="skill-item"><?php echo htmlspecialchars($skill['skill_name']); ?></div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <!-- Fallback skills -->
                  <div class="skill-item">Strategic Planning</div>
                  <div class="skill-item">Project Management</div>
                  <div class="skill-item">Stakeholder Engagements</div>
                  <div class="skill-item">People Management</div>
                  <div class="skill-item">Leadership</div>
                  <div class="skill-item">Proposal Writing</div>
                  <div class="skill-item">Research Design</div>
                  <div class="skill-item">Field Operations</div>
                  <div class="skill-item">Data Analysis</div>
                  <div class="skill-item">Report Writing</div>
                  <div class="skill-item">Programme Design</div>
                  <div class="skill-item">Project Implementation</div>
                  <div class="skill-item">Monitoring and Evaluation</div>
                  <div class="skill-item">Community Mobilization</div>
                  <div class="skill-item">Advanced Digital Skills</div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Experience Section -->
    <section id="experience">
      <div class="container">
        <h2>Professional Experience</h2>
        <div class="timeline">
          <div class="timeline-item">
            <div class="timeline-dot"></div>
            <div class="timeline-content">
              <div class="timeline-header">
                <h3>Senior Research Manager</h3>
                <p class="company">Emdag Consulting Limited – Accra, Ghana</p>
                <p class="date">May 2019 – Present</p>
              </div>
              <ul class="timeline-list">
                <li>
                  Managed five client portfolios including the International
                  Labour Organization, German Development Cooperation, the
                  International Center for Migration Policy Development (ICMPD),
                  among others.
                </li>
                <li>
                  Led and managed the execution of 10 research projects across
                  sectors including education, ICT, renewable energy, labour and
                  construction.
                </li>
                <li>
                  Wrote proposals, crafted research designs, conducted desk
                  reviews, developed questionnaires and collected primary data
                  for different research projects.
                </li>
                <li>
                  Analyzed research evidence or insights from technical data
                  collected to inform programme design, tracking and evaluation.
                </li>
                <li>
                  Disseminated research findings to various personnel in
                  different formats - reports, formal presentations, roundtable
                  discussions, including international workshops.
                </li>
                <li>
                  Managed multicultural teams comprising 3 to 100 members to
                  work together to achieve research outcomes.
                </li>
                <li>
                  Led stakeholder engagements with development partners,
                  international organizations, government institutions,
                  communities and industry stakeholders.
                </li>
                <li>
                  Trained 16 Junior Researchers in research design, fieldwork
                  planning, data collection and research ethics to increase our
                  work efficiency by 25%.
                </li>
              </ul>
            </div>
          </div>

          <div class="timeline-item">
            <div class="timeline-dot"></div>
            <div class="timeline-content">
              <div class="timeline-header">
                <h3>Research Executive</h3>
                <p class="company">
                  1st Klass Research and Consultancy – Accra, Ghana
                </p>
                <p class="date">February 2018 - April 2019</p>
              </div>
              <ul class="timeline-list">
                <li>
                  Pioneered the social research department which increased the
                  company's revenue by 50%.
                </li>
                <li>
                  Directed and coordinated task of teams to collect data across
                  regions in Ghana.
                </li>
                <li>
                  Determined schedules and managed budget for research projects
                  conducted.
                </li>
                <li>
                  Trained about 100 field data collectors and colleagues on
                  research design, methodology and ethics.
                </li>
                <li>
                  Managed data collection on-field and supervised teams of
                  enumerators.
                </li>
                <li>
                  Conducted survey interviews, in-depth interviews and focused
                  group discussions with research subjects and compiled
                  data/notes for review by a team leader.
                </li>
              </ul>
            </div>
          </div>

          <div class="timeline-item">
            <div class="timeline-dot"></div>
            <div class="timeline-content">
              <div class="timeline-header">
                <h3>Freelance Project Supervisor & Enumerator</h3>
                <p class="company">
                  Multiple Organizations (CIC, MSR, ISSER, RIPS, PWC)
                </p>
                <p class="date">February 2011 - December 2018</p>
              </div>
              <ul class="timeline-list">
                <li>
                  Managed data collection on-field and supervised teams of
                  enumerators across regions in Ghana.
                </li>
                <li>
                  Validated methodology and data collected by enumerators during
                  and after interviews to check information accuracy and
                  integrity while independently locating and correcting
                  concerns.
                </li>
                <li>
                  Evaluated potential respondents or survey participants to
                  assess their suitability for planned studies.
                </li>
                <li>
                  Conducted survey interviews, in-depth interviews and focused
                  group discussions with research subjects and compiled
                  data/notes for review by a team leader.
                </li>
              </ul>
            </div>
          </div>

          <div class="timeline-item">
            <div class="timeline-dot"></div>
            <div class="timeline-content">
              <div class="timeline-header">
                <h3>Volunteer - Programmes/Programs Manager</h3>
                <p class="company">Phix Care Ghana (NGO) – Accra, Ghana</p>
                <p class="date">January 2022 – Present</p>
              </div>
              <ul class="timeline-list">
                <li>
                  Developed program activities, budgets, and wrote proposals for
                  funding.
                </li>
                <li>
                  Implemented three (3) outreach programs involving awareness
                  creation, health screening, and cleaning exercises within the
                  Awutu Senya East municipalities for some 500 people.
                </li>
                <li>
                  Organised and made arrangements for five (5) visiting
                  volunteers from USA to participate in various community
                  outreach programs and services.
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Projects Section -->
    <section id="projects">
      <div class="container">
        <h2>Selected Projects</h2>
        <div class="projects-grid">
          <?php if (!empty($projects)): ?>
            <?php foreach ($projects as $project): ?>
              <div class="project-card">
                <div class="project-content">
                  <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                  <?php if ($project['date_completed']): ?>
                    <p class="project-meta"><?php echo date('M Y', strtotime($project['date_completed'])); ?></p>
                  <?php endif; ?>
                  <p><?php echo htmlspecialchars($project['description']); ?></p>
                  <?php if ($project['technologies']): ?>
                    <div class="project-tags">
                      <?php 
                      $tech_array = explode(',', $project['technologies']);
                      foreach ($tech_array as $tech): 
                      ?>
                        <span><?php echo htmlspecialchars(trim($tech)); ?></span>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <!-- Fallback static projects -->
            <div class="project-card">
              <div class="project-content">
                <h3>Incubating Skills Partnerships beneficial to Migrants</h3>
                <p class="project-meta">ICMPD | June 2022 – December 2022</p>
                <p>
                  Ghana case study on prospects for skills partnership, exploring
                  how to create beneficial partnerships for migrants, countries of
                  origin, and destination.
                </p>
                <div class="project-tags">
                  <span>Migration</span>
                  <span>Skills Development</span>
                  <span>Consulting</span>
                </div>
              </div>
            </div>

            <div class="project-card">
              <div class="project-content">
                <h3>Development of ICT Career Pathway</h3>
                <p class="project-meta">
                  GIZ & Commission for TVET | August 2022 – October 2022
                </p>
                <p>
                  Developed comprehensive career pathways for the ICT sector,
                  providing structured guidance for skills development and career
                  progression.
                </p>
                <div class="project-tags">
                  <span>ICT</span>
                  <span>Career Development</span>
                  <span>TVET</span>
                </div>
              </div>
            </div>

            <div class="project-card">
              <div class="project-content">
                <h3>Evaluation of Girls-in-ICT Initiative</h3>
                <p class="project-meta">
                  Ministry of Communication & GIZ | September 2021 – May 2022
                </p>
                <p>
                  Co-led the evaluation of this initiative designed to encourage
                  girls' participation in ICT education and careers.
                </p>
                <div class="project-tags">
                  <span>Gender</span>
                  <span>ICT</span>
                  <span>Evaluation</span>
                </div>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <!-- Education Section -->
    <section id="education">
      <div class="container">
        <h2>Education and Training</h2>
        <div class="education-grid">
          <div class="education-card">
            <div class="education-icon">🎓</div>
            <div class="education-content">
              <h3>Masters of Arts, Development Studies</h3>
              <p class="education-institution">
                University of Ghana - Institute of Statistical, Social and
                Economic Research
              </p>
              <p class="education-date">May 2023</p>
            </div>
          </div>

          <div class="education-card">
            <div class="education-icon">🎓</div>
            <div class="education-content">
              <h3>Postgraduate Diploma in ICT</h3>
              <p class="education-institution">
                Ghana Institute of Management and Public Administration
              </p>
              <p class="education-date">2022</p>
            </div>
          </div>

          <div class="education-card">
            <div class="education-icon">🎓</div>
            <div class="education-content">
              <h3>Bachelor of Arts in Social Work with Sociology</h3>
              <p class="education-institution">University of Ghana</p>
              <p class="education-date">June 2014</p>
            </div>
          </div>

          <div class="education-card">
            <div class="education-icon">🏆</div>
            <div class="education-content">
              <h3>Initiating and Planning Projects Certificate</h3>
              <p class="education-institution">
                University of California, Irvine (USA)
              </p>
              <p class="education-date">April 2016</p>
            </div>
          </div>

          <div class="education-card">
            <div class="education-icon">🏆</div>
            <div class="education-content">
              <h3>YALI Training Certificate</h3>
              <p class="education-institution">
                Young African Leaders Initiative (YALI) Regional Leadership
                Center
              </p>
              <p class="education-date">September 2020</p>
            </div>
          </div>

          <div class="education-card">
            <div class="education-icon">🏆</div>
            <div class="education-content">
              <h3>Microsoft Technology Associate Certificate</h3>
              <p class="education-institution">
                Microsoft Corporation (United States)
              </p>
              <p class="education-date">June 2016</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact Section -->
    <section id="contact">
      <div class="container">
        <h2>Contact Me</h2>
        <div class="contact-container">
          <div class="contact-info">
            <h3>Get in Touch</h3>
            <p>
              Feel free to contact me for consulting opportunities, research
              collaborations, or to discuss how we can work together to drive
              positive change.
            </p>

            <div class="contact-info-item">
              <div class="contact-icon">📍</div>
              <div class="contact-details">
                <h4>Location</h4>
                <p><?php echo htmlspecialchars($about['address'] ?? 'House number 5 Aplite close, West Legon, Accra, Ghana'); ?></p>
              </div>
            </div>

            <div class="contact-info-item">
              <div class="contact-icon">📧</div>
              <div class="contact-details">
                <h4>Email</h4>
                <p><?php echo htmlspecialchars($about['email'] ?? 'ame23dacy@gmail.com'); ?></p>
              </div>
            </div>

            <div class="contact-info-item">
              <div class="contact-icon">📱</div>
              <div class="contact-details">
                <h4>Phone</h4>
                <p><?php echo htmlspecialchars($about['phone'] ?? '+233 240858453 | +233 505360188'); ?></p>
              </div>
            </div>
          </div>

          <div class="contact-form">
            <h3>Send Message</h3>
            <form id="contact-form">
              <div class="form-group">
                <input
                  type="text"
                  name="name"
                  class="form-control"
                  placeholder="Your Name"
                  required
                />
              </div>

              <div class="form-group">
                <input
                  type="email"
                  name="email"
                  class="form-control"
                  placeholder="Your Email"
                  required
                />
              </div>

              <div class="form-group">
                <input type="text" name="subject" class="form-control" placeholder="Subject" />
              </div>

              <div class="form-group">
                <textarea
                  name="message"
                  class="form-control"
                  placeholder="Your Message"
                  required
                ></textarea>
              </div>

              <button type="submit" class="btn">Send Message</button>
            </form>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer>
      <div class="container">
        <div class="footer-content">
          <div class="social-links">
            <a href="#" aria-label="LinkedIn">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                fill="currentColor"
                viewBox="0 0 16 16"
              >
                <path
                  d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854V1.146zm4.943 12.248V6.169H2.542v7.225h2.401zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248-.822 0-1.359.54-1.359 1.248 0 .694.521 1.248 1.327 1.248h.016zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016a5.54 5.54 0 0 1 .016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225h2.4z"
                />
              </svg>
            </a>
            <a href="#" aria-label="Twitter">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                fill="currentColor"
                viewBox="0 0 16 16"
              >
                <path
                  d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z"
                />
              </svg>
            </a>
            <a href="#" aria-label="Research Gate">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                fill="currentColor"
                viewBox="0 0 15 15"
              >
                <path
                  d="M12.5 0h-10C1.1 0 0 1.1 0 2.5v10C0 13.9 1.1 15 2.5 15h10c1.4 0 2.5-1.1 2.5-2.5v-10C15 1.1 13.9 0 12.5 0zM7.9 9.2H6.3V4.8h1.6V9.2zM4.2 3.5c-0.3 0-0.6-0.1-0.8-0.3C3.1 3 3 2.7 3 2.4c0-0.3 0.1-0.6 0.4-0.8C3.6 1.3 3.9 1.2 4.2 1.2c0.3 0 0.6 0.1 0.8 0.3C5.3 1.8 5.4 2.1 5.4 2.4c0 0.3-0.1 0.6-0.4 0.8C4.8 3.4 4.5 3.5 4.2 3.5zM12 9.2h-1.6V7.1c0-0.6-0.1-1.1-0.4-1.4C9.7 5.3 9.3 5.2 8.8 5.2c-0.5 0-0.9 0.2-1.2 0.5c-0.3 0.3-0.4 0.8-0.4 1.4v2.1H5.6V4.8h1.6v0.7c0.2-0.3 0.5-0.5 0.9-0.6c0.4-0.2 0.8-0.2 1.2-0.2c0.8 0 1.4 0.2 1.9 0.7c0.5 0.5 0.7 1.2 0.7 2.1V9.2z"
                />
              </svg>
            </a>
          </div>

          <p class="copyright">© 2025 <?php echo htmlspecialchars($about['name'] ?? 'Daniel Amewuda'); ?>. All Rights Reserved.</p>
        </div>
      </div>
    </footer>

    <!-- JavaScript -->
    <script src="script.js"></script>
  </body>
</html>