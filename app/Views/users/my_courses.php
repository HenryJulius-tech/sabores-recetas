<div class="my-courses-container">
    <!-- Header -->
    <div class="courses-header fade-in">
        <div>
            <h1><i class="bi bi-book-fill me-2"></i>Mis Cursos</h1>
            <p>Cursos en los que estás inscrito</p>
        </div>
        <div class="header-stats">
            <div class="stat-item">
                <div class="stat-number"><?= count($enrolledCourses) ?></div>
                <div class="stat-label">Cursos Inscritos</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?= $approvedCount ?? 0 ?></div>
                <div class="stat-label">Completados</div>
            </div>
        </div>
    </div>

    <?php if (empty($enrolledCourses)): ?>
    <!-- Empty State -->
    <div class="empty-courses-state">
        <div class="empty-icon">
            <i class="bi bi-book"></i>
        </div>
        <h3>Aún no estás inscrito en cursos</h3>
        <p>Explora nuestro catálogo y encuentra los cursos que te interesan</p>
        <a href="<?= url('catalogo') ?>" class="btn btn-primary btn-lg">
            <i class="bi bi-search me-2"></i>Explorar Cursos
        </a>
    </div>
    <?php else: ?>

    <!-- Filter Tabs -->
    <div class="courses-filter-tabs">
        <button class="filter-tab active" onclick="filterCourses('all')">
            <i class="bi bi-grid-3x2 me-2"></i>Todos
        </button>
        <button class="filter-tab" onclick="filterCourses('in-progress')">
            <i class="bi bi-clock-history me-2"></i>En Progreso
        </button>
        <button class="filter-tab" onclick="filterCourses('completed')">
            <i class="bi bi-check-circle me-2"></i>Completados
        </button>
    </div>

    <!-- Courses Grid -->
    <div class="courses-grid">
        <?php foreach ($enrolledCourses as $course): ?>
        <div class="course-card fade-in" data-status="<?= $course['status'] ?>">
            <div class="course-image-container">
                <?php if (!empty($course['image_url'])): ?>
                    <img src="<?= upload_url('courses', $course['image_url']) ?>" alt="<?= e($course['title']) ?>" class="course-image">
                <?php else: ?>
                    <div class="course-image-placeholder">
                        <i class="bi bi-book"></i>
                    </div>
                <?php endif; ?>
                
                <div class="course-overlay">
                    <a href="<?= url('curso/' . $course['id']) ?>" class="btn btn-white">
                        <i class="bi bi-play-circle me-1"></i>Ir al curso
                    </a>
                </div>

                <?php if ($course['status'] === 'approved'): ?>
                    <?php if ($course['progress'] >= 100): ?>
                    <div class="course-badge-completed" style="background:rgba(74,222,128,.15);color:#4ade80">
                        <i class="bi bi-check-circle"></i> Completado
                    </div>
                    <?php elseif ($course['progress'] > 0): ?>
                    <div class="course-badge-completed" style="background:rgba(250,204,21,.15);color:#a16207">
                        <i class="bi bi-arrow-repeat"></i> En Progreso
                    </div>
                    <?php else: ?>
                    <div class="course-badge-completed" style="background:rgba(148,163,184,.15);color:#64748b">
                        <i class="bi bi-play-circle"></i> Sin iniciar
                    </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="course-badge-completed" style="background:rgba(148,163,184,.15);color:#64748b">
                        <i class="bi bi-hourglass-split"></i> Pendiente
                    </div>
                <?php endif; ?>
            </div>

            <div class="course-card-body">
                <h3 class="course-title"><?= e($course['title']) ?></h3>
                <p class="course-instructor">
                    <i class="bi bi-person me-1"></i><?= e($course['instructor']) ?>
                </p>

                <!-- Progress Bar -->
                <div class="progress-section">
                    <div class="progress-header">
                        <span class="progress-label">Progreso</span>
                        <span class="progress-percent"><?= $course['progress'] ?>%</span>
                    </div>
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: <?= $course['progress'] ?>%"></div>
                    </div>
                </div>

                <!-- Course Stats -->
                <div class="course-stats">
                    <div class="stat">
                        <i class="bi bi-clock"></i>
                        <span><?= e($course['duration']) ?></span>
                    </div>
                    <div class="stat">
                        <i class="bi bi-bar-chart"></i>
                        <span><?= ucfirst($course['level']) ?></span>
                    </div>
                    <div class="stat">
                        <i class="bi bi-star-fill"></i>
                        <span><?= mt_rand(42, 50) / 10 ?>/5</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="course-actions">
                    <a href="<?= url('curso/' . $course['id']) ?>" class="btn btn-primary-sm">
                        <i class="bi bi-play-circle me-1"></i>
                        <?= $course['status'] === 'approved' ? 'Revisar' : 'Continuar' ?>
                    </a>
                    <?php if ($course['status'] !== 'approved'): ?>
                        <button class="btn btn-outline-sm" onclick="showCourseResources(<?= $course['id'] ?>)">
                            <i class="bi bi-download me-1"></i>Recursos
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php endif; ?>
</div>

<style>
/* My Courses Page */
.my-courses-container {
    max-width: 1200px;
    margin: 0 auto;
}

.courses-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 32px;
    padding-bottom: 24px;
    border-bottom: 2px solid var(--border);
}

.courses-header h1 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 8px;
}

.courses-header p {
    color: var(--text-muted);
}

.header-stats {
    display: flex;
    gap: 24px;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    color: var(--primary);
}

.stat-label {
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-top: 4px;
}

/* Empty State */
.empty-courses-state {
    text-align: center;
    padding: 80px 40px;
    background: linear-gradient(135deg, #FEF2F2, #FEF7EE);
    border-radius: var(--radius);
    border: 2px dashed var(--border);
}

.empty-icon {
    font-size: 4rem;
    color: var(--primary);
    margin-bottom: 16px;
    opacity: 0.8;
}

.empty-courses-state h3 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 8px;
}

.empty-courses-state p {
    color: var(--text-muted);
    margin-bottom: 24px;
}

/* Filter Tabs */
.courses-filter-tabs {
    display: flex;
    gap: 12px;
    margin-bottom: 32px;
}

.filter-tab {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 10px 18px;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius-xs);
    color: var(--text-muted);
    cursor: pointer;
    font-weight: 500;
    transition: var(--transition);
    font-size: 0.95rem;
}

.filter-tab:hover {
    border-color: var(--primary-light);
    color: var(--primary);
}

.filter-tab.active {
    background: linear-gradient(135deg, var(--primary), #BE123C);
    color: #fff;
    border-color: transparent;
}

/* Courses Grid */
.courses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 24px;
}

.course-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: var(--radius);
    overflow: hidden;
    transition: var(--transition);
    display: flex;
    flex-direction: column;
}

.course-card:hover {
    border-color: var(--primary-light);
    box-shadow: 0 12px 30px rgba(225, 29, 72, 0.1);
    transform: translateY(-4px);
}

.course-image-container {
    position: relative;
    height: 180px;
    overflow: hidden;
    background: linear-gradient(135deg, #f0f0f0, #e0e0e0);
}

.course-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--transition);
}

.course-card:hover .course-image {
    transform: scale(1.08);
}

.course-image-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: #ccc;
}

.course-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: var(--transition);
}

.course-card:hover .course-overlay {
    opacity: 1;
}

.btn-white {
    background: #fff;
    color: var(--text);
    padding: 8px 16px;
    border-radius: var(--radius-xs);
    text-decoration: none;
    font-weight: 600;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-white:hover {
    background: #f0f0f0;
    color: var(--primary);
    text-decoration: none;
}

.course-badge-completed {
    position: absolute;
    top: 12px;
    right: 12px;
    background: linear-gradient(135deg, #10b981, #059669);
    color: #fff;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 4px;
}

.course-card-body {
    padding: 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.course-title {
    font-weight: 700;
    margin-bottom: 8px;
    font-size: 1.05rem;
    color: var(--text);
}

.course-instructor {
    color: var(--text-muted);
    font-size: 0.85rem;
    margin-bottom: 16px;
}

/* Progress Section */
.progress-section {
    margin-bottom: 16px;
}

.progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.progress-label {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--text);
}

.progress-percent {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--primary);
}

.progress-bar-container {
    height: 6px;
    background: #E2E8F0;
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--primary), #FB7185);
    transition: width 0.6s ease;
}

/* Course Stats */
.course-stats {
    display: flex;
    gap: 12px;
    margin-bottom: 16px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--border);
}

.stat {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 0.8rem;
    color: var(--text-muted);
    flex: 1;
}

.stat i {
    color: var(--primary);
}

/* Course Actions */
.course-actions {
    display: flex;
    gap: 8px;
    margin-top: auto;
}

.btn-primary-sm {
    flex: 1;
    padding: 8px 12px;
    background: linear-gradient(135deg, var(--primary), #BE123C);
    color: #fff;
    border: none;
    border-radius: var(--radius-xs);
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-size: 0.9rem;
}

.btn-primary-sm:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(225, 29, 72, 0.3);
    color: #fff;
    text-decoration: none;
}

.btn-outline-sm {
    flex: 1;
    padding: 8px 12px;
    background: #fff;
    color: var(--primary);
    border: 1px solid var(--primary-light);
    border-radius: var(--radius-xs);
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-size: 0.9rem;
}

.btn-outline-sm:hover {
    background: #FEF2F2;
    color: var(--primary-dark);
}

/* Fade In Animation */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in {
    animation: fadeIn 0.5s ease;
}

.course-card.fade-in {
    animation: fadeIn 0.5s ease;
}

/* Responsive */
@media (max-width: 768px) {
    .courses-header {
        flex-direction: column;
        gap: 16px;
    }

    .header-stats {
        width: 100%;
        justify-content: flex-start;
    }

    .courses-grid {
        grid-template-columns: 1fr;
    }

    .courses-filter-tabs {
        flex-wrap: wrap;
    }
}
</style>

<?php $scripts = '
<script>
function filterCourses(status) {
    // Update active tab
    document.querySelectorAll(".filter-tab").forEach(btn => {
        btn.classList.remove("active");
    });
    event.target.closest(".filter-tab").classList.add("active");

    // Filter cards
    const cards = document.querySelectorAll(".course-card");
    cards.forEach(card => {
        const cardStatus = card.dataset.status;
        let show = true;

        if (status === "in-progress" && cardStatus !== "pending") {
            show = false;
        } else if (status === "completed" && cardStatus !== "approved") {
            show = false;
        }

        card.style.display = show ? "" : "none";
        if (show) {
            setTimeout(() => card.style.opacity = "1", 10);
        }
    });
}

function showCourseResources(courseId) {
    alert("Recursos del curso " + courseId);
    // TODO: Implementar modal de recursos
}
</script>
'; ?>
