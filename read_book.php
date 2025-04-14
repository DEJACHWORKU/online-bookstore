<?php
$file = urldecode($_GET['file']);
$file_path = $_SERVER['DOCUMENT_ROOT'] . '/bookstore/book/' . $file;
if (!file_exists($file_path) || strtolower(pathinfo($file_path, PATHINFO_EXTENSION)) !== 'pdf') {
    header("Location: user.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Read Book</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/read book.css">
</head>
<body>
    <header>
        <div class="header-content">
            <a href="user.php" title="Back to Books"><i class="fas fa-arrow-left"></i> <span>Go Back</span></a>
        </div>
        <div class="eye-care-container">
            <button class="eye-care-btn" id="eyeCareToggle" title="Toggle Eye Care Mode"><i class="fas fa-eye"></i></button>
            <div class="intensity-control" id="intensityControl" style="display: none;">
                <label for="intensitySlider">Eye Care Intensity:</label>
                <input type="range" id="intensitySlider" min="0" max="100" value="50">
            </div>
        </div>
        <div class="timer-container" id="timerDisplay">
            <i class="fas fa-clock"></i>
            <div class="timer-inputs" id="timerInputs">
                <input type="number" id="hours" min="0" max="23" placeholder="HH">
                <input type="number" id="minutes" min="0" max="59" placeholder="MM">
                <input type="number" id="seconds" min="0" max="59" placeholder="SS">
                <button class="timer-btn" id="setTimer" title="Set Timer"><i class="fas fa-check"></i></button>
            </div>
            <div class="timer-display" id="timer">
                <div class="timer-progress" id="timerProgress"></div>
                <span class="timer-text" id="timerText">00:00:00</span>
            </div>
            <button class="timer-btn" id="timerControl" title="Start Timer"><i class="fas fa-play"></i></button>
            <button class="timer-btn" id="resetTimer" title="Reset Timer"><i class="fas fa-redo"></i></button>
        </div>
    </header>
    <main>
        <div class="pdf-container" id="pdfViewer"></div>
        <div class="page-info" id="pageInfo"></div>
        <div class="navigation-controls">
            <button class="nav-btn" id="prevPage" title="Previous Page"><i class="fas fa-arrow-left"></i></button>
            <button class="nav-btn" id="nextPage" title="Next Page"><i class="fas fa-arrow-right"></i></button>
        </div>
        <div class="alarm-popup" id="alarmPopup">
            <h3>Time's Up!</h3>
            <p>Your reading session is complete!</p>
            <button id="closeAlarm">Good Work!</button>
        </div>
    </main>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.9.359/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.9.359/pdf.worker.min.js';
        document.addEventListener('DOMContentLoaded', () => {
            const pdfContainer = document.getElementById('pdfViewer');
            const prevPageBtn = document.getElementById('prevPage');
            const nextPageBtn = document.getElementById('nextPage');
            const pageInfo = document.getElementById('pageInfo');
            const timerInputs = document.getElementById('timerInputs');
            const timerDisplay = document.getElementById('timer');
            const timerProgress = document.getElementById('timerProgress');
            const timerText = document.getElementById('timerText');
            const timerControl = document.getElementById('timerControl');
            const setTimerBtn = document.getElementById('setTimer');
            const resetTimerBtn = document.getElementById('resetTimer');
            const hoursInput = document.getElementById('hours');
            const minutesInput = document.getElementById('minutes');
            const secondsInput = document.getElementById('seconds');
            const alarmPopup = document.getElementById('alarmPopup');
            const closeAlarmBtn = document.getElementById('closeAlarm');
            const eyeCareToggle = document.getElementById('eyeCareToggle');
            const intensityControl = document.getElementById('intensityControl');
            const intensitySlider = document.getElementById('intensitySlider');
            let pdfDoc = null;
            let pageNum = 1;
            let pageRendering = false;
            let pageNumPending = null;
            const scale = 1.5;
            let timerInterval = null;
            let remainingTime = 0;
            let totalTime = 0;
            let isTimerRunning = false;
            const bookKey = '<?php echo htmlspecialchars($file); ?>';
            const storageKey = `alarmTime_${bookKey}`;
            const eyeCareStorageKey = `eyeCareSettings_${bookKey}`;
            let isEyeCareActive = false;
            let eyeCareIntensity = 50;
            pdfContainer.addEventListener('contextmenu', (e) => e.preventDefault());
            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && ['c', 'p', 's'].includes(e.key)) {
                    e.preventDefault();
                }
                if (e.key === 'ArrowLeft' || e.key === 'PageUp') {
                    prevPageBtn.click();
                } else if (e.key === 'ArrowRight' || e.key === 'PageDown') {
                    nextPageBtn.click();
                }
            });
            document.addEventListener('copy', (e) => e.preventDefault());
            document.addEventListener('dragstart', (e) => e.preventDefault());
            function formatTime(seconds) {
                const hrs = Math.floor(seconds / 3600);
                const mins = Math.floor((seconds % 3600) / 60);
                const secs = seconds % 60;
                return `${hrs.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            }
            function updateProgress() {
                const progress = totalTime > 0 ? (1 - remainingTime / totalTime) * 100 : 0;
                timerProgress.style.width = `${progress}%`;
            }
            function setTimer() {
                const hours = parseInt(hoursInput.value || 0, 10);
                const minutes = parseInt(minutesInput.value || 0, 10);
                const seconds = parseInt(secondsInput.value || 0, 10);
                remainingTime = hours * 3600 + minutes * 60 + seconds;
                if (remainingTime <= 0) {
                    alert('Please set a valid time.');
                    return;
                }
                totalTime = remainingTime;
                timerText.textContent = formatTime(remainingTime);
                localStorage.setItem(storageKey, JSON.stringify({ time: remainingTime, total: totalTime, running: false }));
                timerInputs.style.display = 'none';
                timerDisplay.style.display = 'flex';
                timerControl.style.display = 'inline';
                resetTimerBtn.style.display = 'inline';
                timerControl.innerHTML = '<i class="fas fa-play"></i>';
                timerControl.title = 'Start Timer';
                isTimerRunning = false;
                updateProgress();
            }
            function startTimer() {
                if (!isTimerRunning && remainingTime > 0) {
                    isTimerRunning = true;
                    timerInterval = setInterval(() => {
                        remainingTime--;
                        timerText.textContent = formatTime(remainingTime);
                        updateProgress();
                        localStorage.setItem(storageKey, JSON.stringify({ time: remainingTime, total: totalTime, running: true }));
                        if (remainingTime <= 0) {
                            clearInterval(timerInterval);
                            isTimerRunning = false;
                            alarmPopup.classList.add('show');
                            const audio = new Audio('https://www.soundjay.com/buttons/beep-01a.mp3');
                            audio.play();
                            timerControl.innerHTML = '<i class="fas fa-play"></i>';
                            timerControl.title = 'Start Timer';
                            localStorage.removeItem(storageKey);
                        }
                    }, 1000);
                    timerControl.innerHTML = '<i class="fas fa-pause"></i>';
                    timerControl.title = 'Pause Timer';
                }
            }
            function pauseTimer() {
                if (isTimerRunning) {
                    isTimerRunning = false;
                    clearInterval(timerInterval);
                    localStorage.setItem(storageKey, JSON.stringify({ time: remainingTime, total: totalTime, running: false }));
                    timerControl.innerHTML = '<i class="fas fa-play"></i>';
                    timerControl.title = 'Resume Timer';
                }
            }
            function toggleTimer() {
                if (isTimerRunning) {
                    pauseTimer();
                } else {
                    startTimer();
                }
            }
            function resetTimer() {
                clearInterval(timerInterval);
                isTimerRunning = false;
                remainingTime = 0;
                totalTime = 0;
                timerText.textContent = '00:00:00';
                timerProgress.style.width = '0%';
                timerInputs.style.display = 'inline-flex';
                timerDisplay.style.display = 'none';
                timerControl.style.display = 'none';
                resetTimerBtn.style.display = 'none';
                hoursInput.value = '';
                minutesInput.value = '';
                secondsInput.value = '';
                localStorage.removeItem(storageKey);
            }
            const savedAlarm = localStorage.getItem(storageKey);
            if (savedAlarm) {
                const { time, total, running } = JSON.parse(savedAlarm);
                remainingTime = time;
                totalTime = total;
                timerText.textContent = formatTime(remainingTime);
                timerInputs.style.display = 'none';
                timerDisplay.style.display = 'flex';
                timerControl.style.display = 'inline';
                resetTimerBtn.style.display = 'inline';
                updateProgress();
                if (running) {
                    startTimer();
                } else {
                    timerControl.innerHTML = '<i class="fas fa-play"></i>';
                    timerControl.title = 'Resume Timer';
                }
            } else {
                timerDisplay.style.display = 'none';
                timerControl.style.display = 'none';
                resetTimerBtn.style.display = 'none';
            }
            setTimerBtn.addEventListener('click', setTimer);
            timerControl.addEventListener('click', toggleTimer);
            resetTimerBtn.addEventListener('click', resetTimer);
            closeAlarmBtn.addEventListener('click', () => {
                alarmPopup.classList.remove('show');
                resetTimer();
            });
            function applyEyeCareFilter(intensity) {
                const filterValue = `sepia(${intensity}%) brightness(${90 - intensity / 5}%) contrast(100%)`;
                document.body.style.filter = filterValue;
                pdfContainer.classList.add('eye-care-mode');
                if (pdfDoc) {
                    renderPage(pageNum);
                }
            }
            function removeEyeCareFilter() {
                document.body.style.filter = 'none';
                pdfContainer.classList.remove('eye-care-mode');
                if (pdfDoc) {
                    renderPage(pageNum);
                }
            }
            function updateEyeCareSettings() {
                if (isEyeCareActive) {
                    applyEyeCareFilter(eyeCareIntensity);
                    intensityControl.style.display = 'flex';
                    eyeCareToggle.classList.add('active');
                } else {
                    removeEyeCareFilter();
                    intensityControl.style.display = 'none';
                    eyeCareToggle.classList.remove('active');
                }
                localStorage.setItem(eyeCareStorageKey, JSON.stringify({
                    active: isEyeCareActive,
                    intensity: eyeCareIntensity
                }));
            }
            eyeCareToggle.addEventListener('click', () => {
                isEyeCareActive = !isEyeCareActive;
                updateEyeCareSettings();
            });
            intensitySlider.addEventListener('input', () => {
                eyeCareIntensity = parseInt(intensitySlider.value, 10);
                if (isEyeCareActive) {
                    applyEyeCareFilter(eyeCareIntensity);
                }
                localStorage.setItem(eyeCareStorageKey, JSON.stringify({
                    active: isEyeCareActive,
                    intensity: eyeCareIntensity
                }));
            });
            const savedEyeCareSettings = localStorage.getItem(eyeCareStorageKey);
            if (savedEyeCareSettings) {
                const { active, intensity } = JSON.parse(savedEyeCareSettings);
                isEyeCareActive = active;
                eyeCareIntensity = intensity;
                intensitySlider.value = intensity;
                updateEyeCareSettings();
            }
            function renderPage(num) {
                pageRendering = true;
                pdfDoc.getPage(num).then(page => {
                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    const viewport = page.getViewport({ scale: scale });
                    const outputScale = window.devicePixelRatio || 1;
                    canvas.width = Math.floor(viewport.width * outputScale);
                    canvas.height = Math.floor(viewport.height * outputScale);
                    canvas.style.width = "100%";
                    canvas.style.height = "auto";
                    canvas.style.backgroundColor = '#ffffff';
                    const transform = outputScale !== 1
                        ? [outputScale, 0, 0, outputScale, 0, 0]
                        : null;
                    pdfContainer.innerHTML = '';
                    pdfContainer.appendChild(canvas);
                    const textLayerDiv = document.createElement('div');
                    textLayerDiv.className = 'textLayer';
                    textLayerDiv.style.width = `${viewport.width}px`;
                    textLayerDiv.style.height = `${viewport.height}px`;
                    textLayerDiv.style.transform = `scale(${canvas.width / viewport.width}, ${canvas.height / viewport.height})`;
                    textLayerDiv.style.transformOrigin = '0 0';
                    pdfContainer.appendChild(textLayerDiv);
                    const renderContext = {
                        canvasContext: context,
                        viewport: viewport,
                        transform: transform,
                        textLayer: {
                            textLayerDiv: textLayerDiv,
                            viewport: viewport,
                            textContentSource: page.getTextContent(),
                        },
                        textLayerMode: 2,
                        annotationLayerMode: 0
                    };
                    page.render(renderContext).promise.then(() => {
                        pageRendering = false;
                        if (pageNumPending !== null) {
                            renderPage(pageNumPending);
                            pageNumPending = null;
                        }
                        pageInfo.textContent = `Page ${num} of ${pdfDoc.numPages}`;
                        pageInfo.classList.add('show');
                        setTimeout(() => {
                            pageInfo.classList.remove('show');
                        }, 2000);
                    });
                    prevPageBtn.disabled = num <= 1;
                    nextPageBtn.disabled = num >= pdfDoc.numPages;
                });
            }
            function queueRenderPage(num) {
                if (pageRendering) {
                    pageNumPending = num;
                } else {
                    renderPage(num);
                }
            }
            pdfjsLib.getDocument({
                url: '/bookstore/book/<?php echo htmlspecialchars($file); ?>',
                cMapUrl: 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.9.359/cmaps/',
                cMapPacked: true
            }).promise.then(pdf => {
                pdfDoc = pdf;
                renderPage(pageNum);
                if (window.innerWidth <= 768) {
                    pdfContainer.style.height = `calc(100vh - 40px)`;
                }
            }).catch(error => {
                pdfContainer.innerHTML = '<div class="error-message">Unable to load the book. Please try again later.</div>';
                console.error('PDF loading error:', error);
            });
            prevPageBtn.addEventListener('click', () => {
                if (pageNum <= 1) return;
                pageNum--;
                queueRenderPage(pageNum);
            });
            nextPageBtn.addEventListener('click', () => {
                if (pageNum >= pdfDoc.numPages) return;
                pageNum++;
                queueRenderPage(pageNum);
            });
            window.addEventListener('resize', () => {
                if (pdfDoc) {
                    renderPage(pageNum);
                }
            });
            window.addEventListener('orientationchange', () => {
                setTimeout(() => {
                    if (pdfDoc) {
                        renderPage(pageNum);
                    }
                }, 500);
            });
        });
    </script>
</body>
</html>