<?php
session_start();

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || !isset($_SESSION['user_id'])) {
    header("Location: login1.php");
    exit();
}

if (!isset($_GET['file']) || empty($_GET['file'])) {
    header("Location: userpage.php");
    exit();
}

$file = urldecode($_GET['file']);
$file_path = $_SERVER['DOCUMENT_ROOT'] . '/bookstore/book/' . $file;

if (!file_exists($file_path) || strtolower(pathinfo($file_path, PATHINFO_EXTENSION)) !== 'pdf') {
    header("Location: userpage.php");
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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.4s ease-in-out;
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            overscroll-behavior: none;
            touch-action: manipulation;
        }

        header {
            background-color: #2c3e50;
            padding: 0.6rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
            position: sticky;
            top: 0;
            z-index: 1000;
            height: 48px;
            border-bottom: 3px solid #ffca28;
        }

        .header-content {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .header-content a {
            font-size: 1.2rem;
            font-weight: 700;
            color: #ffffff;
            text-decoration: none;
            letter-spacing: 0.02em;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
            transition: opacity 0.3s ease, transform 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .header-content a:hover {
            opacity: 0.85;
            transform: translateY(-2px);
        }

        .timer-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #ffffff33, #ffca2833);
            padding: 0.4rem 0.8rem;
            border-radius: 50px;
            color: #ffffff;
            font-size: 0.9rem;
            font-weight: 600;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.3);
        }

        .timer-inputs {
            display: flex;
            gap: 0.3rem;
        }

        .timer-inputs input {
            width: 40px;
            padding: 0.3rem;
            border: none;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.4);
            color: #ffffff;
            font-size: 0.8rem;
            text-align: center;
            transition: background 0.3s ease;
        }

        .timer-inputs input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.6);
        }

        .timer-display {
            position: relative;
            width: 60px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(90deg, #ff4d4d, #00e6b8, #1e90ff);
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
            overflow: hidden;
            animation: glow 2s infinite alternate;
        }

        @keyframes glow {
            from { box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4); }
            to { box-shadow: 0 6px 20px rgba(255, 107, 107, 0.6); }
        }

        .timer-progress {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            transition: width 1s linear;
        }

        .timer-text {
            position: relative;
            z-index: 1;
            font-size: 0.8rem;
            font-weight: 700;
            color: #ffffff;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
        }

        .timer-btn {
            background: none;
            border: none;
            color: #ffffff;
            cursor: pointer;
            padding: 0.3rem;
            transition: all 0.3s ease;
        }

        .timer-btn:hover {
            color: #ffca28;
            transform: scale(1.2);
        }

        .alarm-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: linear-gradient(135deg, #ff6b6b, #ffca28);
            color: #ffffff;
            padding: 2rem;
            border-radius: 16px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
            z-index: 2000;
            text-align: center;
            display: none;
            animation: popIn 0.5s ease;
            border: 3px solid #ffffff;
        }

        .alarm-popup.show {
            display: block;
        }

        .alarm-popup h3 {
            font-size: 1.6rem;
            margin-bottom: 0.6rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .alarm-popup p {
            font-size: 1.1rem;
            margin-bottom: 1.2rem;
        }

        .alarm-popup button {
            background: #1e3c72;
            color: #ffffff;
            border: none;
            padding: 0.7rem 1.4rem;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.3s ease, background 0.3s ease;
        }

        .alarm-popup button:hover {
            background: #2a5298;
            transform: scale(1.1);
        }

        @keyframes popIn {
            from { opacity: 0; transform: translate(-50%, -60%) scale(0.8); }
            to { opacity: 1; transform: translate(-50%, -50%) scale(1); }
        }

        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0;
            position: relative;
            margin: 0;
            background: #ffffff;
        }

        .pdf-container {
            width: 100vm;
     
            position: relative;
          
            left: 0;
           overflow: hidden;
            background: #ffffff;
        }

        .pdf-container canvas {
            width: 100%;
            height: auto;
            object-fit: contain;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
            background-color: #ffffff;
            border-radius: 6px;
        }

        .pdf-container .textLayer {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            color: #1e3c72;
            opacity: 1;
        }

        .pdf-container .textLayer > div {
            color: #1e3c72 !important;
        }

        .navigation-controls {
            position: fixed;
            bottom: 1rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 1rem;
            background: linear-gradient(135deg, #ff6b6b88, #ffca2888);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
            z-index: 1000;
        }

        .nav-btn {
            background: none;
            border: none;
            color: #ffffff;
            font-size: 1.4rem;
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 0.5rem;
        }

        .nav-btn:hover {
            color: #ffca28;
            transform: scale(1.2);
        }

        .nav-btn:disabled {
            color: #aaaaaa;
            cursor: not-allowed;
        }

        .page-info {
            position: fixed;
            bottom: 4rem;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #ff6b6b88, #ffca2888);
            color: #ffffff;
            padding: 0.6rem 1.2rem;
            border-radius: 20px;
            font-size: 0.9rem;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .page-info.show {
            opacity: 1;
        }

        .error-message {
            text-align: center;
            padding: 2rem;
            color: #1e3c72;
            font-size: 1.3rem;
            font-weight: 500;
            max-width: 90%;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        @media (max-width: 768px) {
            header {
                padding: 0.5rem 1rem;
                height: 40px;
            }

            .header-content a {
                font-size: 1rem;
            }

            .header-content a i {
                font-size: 0.9rem;
            }

            .timer-container {
                padding: 0.3rem 0.6rem;
                font-size: 0.8rem;
            }

            .timer-inputs input {
                width: 35px;
                font-size: 0.7rem;
            }

            .timer-display {
                width: 50px;
                height: 25px;
            }

            .timer-text {
                font-size: 0.7rem;
            }

            .timer-btn {
                padding: 0.2rem;
            }

            .pdf-container {
                height: calc(100vh - 40px);
                top: 40px;
            }

            .navigation-controls {
                padding: 0.4rem 0.8rem;
                gap: 0.8rem;
                bottom: 0.5rem;
            }

            .nav-btn {
                font-size: 1.2rem;
                padding: 0.4rem;
            }

            .page-info {
                bottom: 3rem;
                font-size: 0.8rem;
            }

            .alarm-popup {
                width: 80%;
                padding: 1.5rem;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .timer-container {
                padding: 0.2rem 0.5rem;
                font-size: 0.75rem;
            }

            .timer-inputs input {
                width: 30px;
            }

            .timer-display {
                width: 45px;
            }

            .navigation-controls {
                width: 90%;
                justify-content: space-around;
                gap: 0;
            }

            .error-message {
                font-size: 1.1rem;
            }
        }

        @media (max-width: 360px) {
            .header-content a span {
                display: none;
            }

            .header-content a i {
                font-size: 1rem;
            }

            .timer-container {
                gap: 0.3rem;
            }

            .timer-inputs input {
                width: 25px;
                font-size: 0.65rem;
            }

            .timer-display {
                width: 40px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <a href="user.php" title="Back to Books"><i class="fas fa-arrow-left"></i> <span>Go Back</span></a>
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
            <button id="closeAlarm">Awesome!</button>
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