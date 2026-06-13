(function() {
    var TIMEOUT = 300000;
    var timer;

    function startTimer() {
        clearTimeout(timer);
        timer = setTimeout(function() {
            window.location.href = 'logout.php?expired=1';
        }, TIMEOUT);
    }

    startTimer();

    window.addEventListener('mousemove', startTimer);
    window.addEventListener('keydown', startTimer);
    window.addEventListener('scroll', startTimer);
    window.addEventListener('click', startTimer);
    window.addEventListener('touchstart', startTimer);
})();
