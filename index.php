<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://fonts.googleapis.com/css?family=Ubuntu+Mono:400,700" rel="stylesheet">
</head>
<body>
<div>
  <h1 class="cssanimation leSnake sequence">MineEase...</h1>
  <!-- <h1 class="cssanimation leSnake random">cssanimation</h1>
  please uncomment if you want to see random version -->
</div>
</body>
<style>
    /**
 * cssanimation.css
 * https://www.cssanimatio.io
 * Created and maintained by: Pavel
 * Find me at: https://www.linkedin.com/in/yesiamrocks/
 * Email: hello@cssanimation.io
 * Github: https://github.com/yesiamrocks/cssanimation.io
 * Title: A CSS Animation Library for Developers and Ninjas
 * Copyright (c) 2017 Pavel
 * License: cssanimation.io is licensed under the MIT license
 **/

body {
    overflow: hidden;
    font-family: 'Ubuntu', sans-serif;
}

div {
    width: 100%;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translateX(-50%) translateY(-50%);
}

h1 {
    font-size: 4.3em;
    letter-spacing: -4px;
    font-weight: 700;
    color: #001808;
    text-align: center;
}

.cssanimation, .cssanimation span {
    animation-duration: 1s;
    animation-fill-mode: both;
}

.cssanimation span { display: inline-block }

.leSnake span { animation: leSnake 1.5s ease-in-out; animation-iteration-count: infinite }
@keyframes leSnake {
    from, to { transform: translateY(0px) }
    50% { transform: translateY(30px) }
}
</style>

<script>
window.onload = function() {
    animateSequence();
    animateRandom();
    
    // Set timeout for redirection after 10 seconds (10000 milliseconds)
    setTimeout(function() {
        // Replace 'your-destination-page.html' with the URL you want to redirect to
        window.location.href = 'log/log.php';
    }, 10000);
};

function animateSequence() {
    var a = document.getElementsByClassName('sequence');
    for (var i = 0; i < a.length; i++) {
        var $this = a[i];
        var letter = $this.innerHTML;
        letter = letter.trim();
        var str = '';
        var delay = 100;
        for (l = 0; l < letter.length; l++) {
            if (letter[l] != ' ') {
                str += '<span style="animation-delay:' + delay + 'ms; -moz-animation-delay:' + delay + 'ms; -webkit-animation-delay:' + delay + 'ms; ">' + letter[l] + '</span>';
                delay += 150;
            } else
                str += letter[l];
        }
        $this.innerHTML = str;
    }
}

function animateRandom() {
    var a = document.getElementsByClassName('random');
    for (var i = 0; i < a.length; i++) {
        var $this = a[i];
        var letter = $this.innerHTML;
        letter = letter.trim();
        var delay = 70;
        var delayArray = new Array;
        var randLetter = new Array;
        for (j = 0; j < letter.length; j++) {
            while (1) {
                var random = getRandomInt(0, (letter.length - 1));
                if (delayArray.indexOf(random) == -1)
                    break;
            }
            delayArray[j] = random;
        }
        for (l = 0; l < delayArray.length; l++) {
            var str = '';
            var index = delayArray[l];
            if (letter[index] != ' ') {
                str = '<span style="animation-delay:' + delay + 'ms; -moz-animation-delay:' + delay + 'ms; -webkit-animation-delay:' + delay + 'ms; ">' + letter[index] + '</span>';
                randLetter[index] = str;
            } else
                randLetter[index] = letter[index];
            delay += 80;
        }
        randLetter = randLetter.join("");
        $this.innerHTML = randLetter;
    }
}

function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}
</script>
</html>