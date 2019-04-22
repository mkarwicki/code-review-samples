var canvasEvent;
$(function () {
    $('#functionsCanvas').on('click', function (event) {
        $('.function.active').removeClass('active');
        $('.function.hovered').addClass('activated');
        $('.function.hovered').addClass('active');
        $('.functionBox').removeClass('active');
        $('.functionBox.' + $('.function.active').data('name')).addClass('active');
        canvas.onmousemove(canvasEvent);
    })
    if ($('#functionsCanvas').length > 0) {
        var canvas = document.getElementById('functionsCanvas');
        var context = canvas.getContext('2d');
        context.beginPath();
        context.moveTo(265, 22);
        context.lineTo(266, 266);
        context.lineTo(389, 58);
        context.quadraticCurveTo(333, 25, 265, 22);
        context.closePath();
        context.fillStyle = 'rgba(237,183,0,0.1)';
        context.fill();
        context.beginPath();
        context.moveTo(389, 58);
        context.lineTo(266, 266);
        context.lineTo(473, 146);
        context.quadraticCurveTo(450, 100, 389, 58);
        context.closePath();
        context.fillStyle = 'rgba(255,90,0,0.1)';
        context.fill();
        context.beginPath();
        context.moveTo(473, 146);
        context.lineTo(266, 266);
        context.lineTo(504, 263);
        context.quadraticCurveTo(502, 204, 473, 146);
        context.closePath();
        context.fillStyle = 'rgba(243,40,55,0.1)';
        context.fill();
        context.beginPath();
        context.moveTo(504, 263);
        context.lineTo(266, 266);
        context.lineTo(473, 385);
        context.quadraticCurveTo(505, 325, 504, 263);
        context.closePath();
        context.fillStyle = 'rgba(204,82,135,0.1)';
        context.fill();
        context.beginPath();
        context.moveTo(473, 385);
        context.lineTo(266, 266);
        context.lineTo(385, 475);
        context.quadraticCurveTo(442, 438, 473, 385);
        context.closePath();
        context.fillStyle = 'rgba(143,43,121,0.1)';
        context.fill();
        context.beginPath();
        context.moveTo(385, 475);
        context.lineTo(266, 266);
        context.lineTo(265, 508);
        context.quadraticCurveTo(336, 505, 385, 475);
        context.closePath();
        context.fillStyle = 'rgba(81,34,115,0.1)';
        context.fill();
        context.beginPath();
        context.moveTo(265, 508);
        context.lineTo(266, 266);
        context.lineTo(143, 474);
        context.quadraticCurveTo(201, 508, 265, 508);
        context.closePath();
        context.fillStyle = 'rgba(0,28,168,0.1)';
        context.fill();
        context.beginPath();
        context.moveTo(143, 474);
        context.lineTo(266, 266);
        context.lineTo(58, 383);
        context.quadraticCurveTo(88, 440, 143, 474);
        context.closePath();
        context.fillStyle = 'rgba(0,90,132,0.1)';
        context.fill();
        context.beginPath();
        context.moveTo(58, 383);
        context.lineTo(266, 266);
        context.lineTo(25, 267);
        context.quadraticCurveTo(30, 328, 58, 383);
        context.closePath();
        context.fillStyle = 'rgba(0,153,116,0.1)';
        context.fill();
        context.beginPath();
        context.moveTo(25, 267);
        context.lineTo(266, 266);
        context.lineTo(59, 145);
        context.quadraticCurveTo(28, 195, 25, 267);
        context.closePath();
        context.fillStyle = 'rgba(61,155,53,0.1)';
        context.fill();
        context.beginPath();
        context.moveTo(59, 145);
        context.lineTo(266, 266);
        context.lineTo(144, 55);
        context.quadraticCurveTo(84, 95, 59, 145);
        context.closePath();
        context.fillStyle = 'rgba(190,214,0,0.1)';
        context.fill();
        context.beginPath();
        context.moveTo(144, 55);
        context.lineTo(266, 266);
        context.lineTo(264, 22);
        context.quadraticCurveTo(200, 18, 144, 55);
        context.closePath();
        context.fillStyle = 'rgba(254,224,0,0.1)';
        context.fill();
        canvas.onmousemove = function (e) {

            canvasEvent = e;
            var test = 0;
            $('#logaTimeFunctions .function.hovered').removeClass('hovered');
            var canvas = e.target;
            var context = canvas.getContext('2d');
            context.clearRect(0, 0, canvas.width, canvas.height);
            // This gets the mouse coordinates (relative to the canvas)
            var mouseXY = RGraph.getMouseXY(e);
            var mouseX = mouseXY[0];
            var mouseY = mouseXY[1];
            context.beginPath();
            context.moveTo(265, 22);
            context.lineTo(266, 266);
            context.lineTo(389, 58);
            context.quadraticCurveTo(333, 25, 265, 22);
            context.closePath();
            if (context.isPointInPath(mouseX, mouseY)) {
                canvas.style.cursor = 'pointer';
                test = 1;
                context.fillStyle = 'rgba(237,183,0,0.2)';
                context.fill();
                $('.function.parametryzacja').addClass('hovered')
            } else {
                if (test == 0) {
                    canvas.style.cursor = 'default';
                }
                context.fillStyle = 'rgba(237,183,0,0.1)';
                context.fill();
            }
            if ($('.function.parametryzacja').hasClass('active')) {
                context.lineWidth = 3;
                context.strokeStyle = '#000';
                context.fillStyle = 'rgba(237,183,0,1)';
                context.fill();
                context.stroke();
            } else if ($('.function.parametryzacja').hasClass('activated')) {
                context.fillStyle = 'rgba(237,183,0,1)';
                context.fill();
            }
            context.beginPath();
            context.moveTo(389, 58);
            context.lineTo(266, 266);
            context.lineTo(473, 146);
            context.quadraticCurveTo(450, 100, 389, 58);
            context.closePath();
            if (context.isPointInPath(mouseX, mouseY)) {
                canvas.style.cursor = 'pointer';
                test = 1;
                context.fillStyle = 'rgba(255,90,0,0.2)';
                context.fill();
                $('.function.planowanie').addClass('hovered')
            } else {
                if (test == 0) {
                    canvas.style.cursor = 'default';
                }
                context.fillStyle = 'rgba(255,90,0,0.1)';
                context.fill();
            }
            if ($('.function.planowanie').hasClass('active')) {
                context.lineWidth = 3;
                context.strokeStyle = '#000';
                context.fillStyle = 'rgba(255,90,0,1)';
                context.fill();
                context.stroke();
            } else if ($('.function.planowanie').hasClass('activated')) {
                context.fillStyle = 'rgba(255,90,0,1)';
                context.fill();
            }
            context.beginPath();
            context.moveTo(473, 146);
            context.lineTo(266, 266);
            context.lineTo(504, 263);
            context.quadraticCurveTo(502, 204, 473, 146);
            context.closePath();
            if (context.isPointInPath(mouseX, mouseY)) {
                canvas.style.cursor = 'pointer';
                test = 1;
                context.fillStyle = 'rgba(243,40,55,0.2)';
                context.fill();
                $('.function.rejestracja').addClass('hovered')
            } else {
                if (test == 0) {
                    canvas.style.cursor = 'default';
                }
                context.fillStyle = 'rgba(243,40,55,0.1)';
                context.fill();
            }
            if ($('.function.rejestracja').hasClass('active')) {
                context.lineWidth = 3;
                context.strokeStyle = '#000';
                context.fillStyle = 'rgba(243,40,55,1)';
                context.fill();
                context.stroke();
            } else if ($('.function.rejestracja').hasClass('activated')) {
                context.fillStyle = 'rgba(243,40,55,1)';
                context.fill();
            }
            context.beginPath();
            context.moveTo(504, 263);
            context.lineTo(266, 266);
            context.lineTo(473, 385);
            context.quadraticCurveTo(505, 325, 504, 263);
            context.closePath();
            if (context.isPointInPath(mouseX, mouseY)) {
                canvas.style.cursor = 'pointer';
                test = 1;
                context.fillStyle = 'rgba(204,82,135,0.2)';
                context.fill();
                $('.function.kontrolaDostepu').addClass('hovered')
            } else {
                if (test == 0) {
                    canvas.style.cursor = 'default';
                }
                context.fillStyle = 'rgba(204,82,135,0.1)';
                context.fill();
            }
            if ($('.function.kontrolaDostepu').hasClass('active')) {
                context.lineWidth = 3;
                context.strokeStyle = '#000';
                context.fillStyle = 'rgba(204,82,135,1)';
                context.fill();
                context.stroke();
            } else if ($('.function.kontrolaDostepu').hasClass('activated')) {
                context.fillStyle = 'rgba(204,82,135,1)';
                context.fill();
            }
            context.beginPath();
            context.moveTo(473, 385);
            context.lineTo(266, 266);
            context.lineTo(385, 475);
            context.quadraticCurveTo(442, 438, 473, 385);
            context.closePath();
            if (context.isPointInPath(mouseX, mouseY)) {
                canvas.style.cursor = 'pointer';
                test = 1;
                context.fillStyle = 'rgba(143,43,121,0.2)';
                context.fill();
                $('.function.automatycznePrzetwarzanie').addClass('hovered')
            } else {
                if (test == 0) {
                    canvas.style.cursor = 'default';
                }
                context.fillStyle = 'rgba(143,43,121,0.1)';
                context.fill();
            }
            if ($('.function.automatycznePrzetwarzanie').hasClass('active')) {
                context.lineWidth = 3;
                context.strokeStyle = '#000';
                context.fillStyle = 'rgba(143,43,121,1)';
                context.fill();
                context.stroke();
            } else if ($('.function.automatycznePrzetwarzanie').hasClass('activated')) {
                context.fillStyle = 'rgba(143,43,121,1)';
                context.fill();
            }
            context.beginPath();
            context.moveTo(385, 475);
            context.lineTo(266, 266);
            context.lineTo(265, 508);
            context.quadraticCurveTo(336, 505, 385, 475);
            context.closePath();
            if (context.isPointInPath(mouseX, mouseY)) {
                canvas.style.cursor = 'pointer';
                test = 1;
                context.fillStyle = 'rgba(81,34,115,0.2)';
                context.fill();
                $('.function.rozliczanieKosztow').addClass('hovered')
            } else {
                if (test == 0) {
                    canvas.style.cursor = 'default';
                }
                context.fillStyle = 'rgba(81,34,115,0.1)';
                context.fill();
            }
            if ($('.function.rozliczanieKosztow').hasClass('active')) {
                context.lineWidth = 3;
                context.strokeStyle = '#000';
                context.fillStyle = 'rgba(81,34,115,1)';
                context.fill();
                context.stroke();
            } else if ($('.function.rozliczanieKosztow').hasClass('activated')) {
                context.fillStyle = 'rgba(81,34,115,1)';
                context.fill();
            }
            context.beginPath();
            context.moveTo(265, 508);
            context.lineTo(266, 266);
            context.lineTo(143, 474);
            context.quadraticCurveTo(201, 508, 265, 508);
            context.closePath();
            if (context.isPointInPath(mouseX, mouseY)) {
                canvas.style.cursor = 'pointer';
                test = 1;
                context.fillStyle = 'rgba(0,28,168,0.2)';
                context.fill();
                $('.function.rozliczanieProjektow').addClass('hovered')
            } else {
                if (test == 0) {
                    canvas.style.cursor = 'default';
                }
                context.fillStyle = 'rgba(0,28,168,0.1)';
                context.fill();
            }
            if ($('.function.rozliczanieProjektow').hasClass('active')) {
                context.lineWidth = 3;
                context.strokeStyle = '#000';
                context.fillStyle = 'rgba(0,28,168,1)';
                context.fill();
                context.stroke();
            } else if ($('.function.rozliczanieProjektow').hasClass('activated')) {
                context.fillStyle = 'rgba(0,28,168,1)';
                context.fill();
            }
            context.beginPath();
            context.moveTo(143, 474);
            context.lineTo(266, 266);
            context.lineTo(58, 383);
            context.quadraticCurveTo(88, 440, 143, 474);
            context.closePath();
            if (context.isPointInPath(mouseX, mouseY)) {
                canvas.style.cursor = 'pointer';
                test = 1;
                context.fillStyle = 'rgba(0,90,132,0.2)';
                context.fill();
                $('.function.zarzadzanieNadgodzinami').addClass('hovered')
            } else {
                if (test == 0) {
                    canvas.style.cursor = 'default';
                }
                context.fillStyle = 'rgba(0,90,132,0.1)';
                context.fill();
            }
            if ($('.function.zarzadzanieNadgodzinami').hasClass('active')) {
                context.lineWidth = 3;
                context.strokeStyle = '#000';
                context.fillStyle = 'rgba(0,90,132,1)';
                context.fill();
                context.stroke();
            } else if ($('.function.zarzadzanieNadgodzinami').hasClass('activated')) {
                context.fillStyle = 'rgba(0,90,132,1)';
                context.fill();
            }
            context.beginPath();
            context.moveTo(58, 383);
            context.lineTo(266, 266);
            context.lineTo(25, 267);
            context.quadraticCurveTo(30, 328, 58, 383);
            context.closePath();
            if (context.isPointInPath(mouseX, mouseY)) {
                canvas.style.cursor = 'pointer';
                test = 1;
                context.fillStyle = 'rgba(0,153,116,0.2)';
                context.fill();
                $('.function.bilansowanieCzasuPracy').addClass('hovered')
            } else {
                if (test == 0) {
                    canvas.style.cursor = 'default';
                }
                context.fillStyle = 'rgba(0,153,116,0.1)';
                context.fill();
            }
            if ($('.function.bilansowanieCzasuPracy').hasClass('active')) {
                context.lineWidth = 3;
                context.strokeStyle = '#000';
                context.fillStyle = 'rgba(0,153,116,1)';
                context.fill();
                context.stroke();
            } else if ($('.function.bilansowanieCzasuPracy').hasClass('activated')) {
                context.fillStyle = 'rgba(0,153,116,1)';
                context.fill();
            }
            context.beginPath();
            context.moveTo(25, 267);
            context.lineTo(266, 266);
            context.lineTo(59, 145);
            context.quadraticCurveTo(28, 195, 25, 267);
            context.closePath();
            if (context.isPointInPath(mouseX, mouseY)) {
                canvas.style.cursor = 'pointer';
                test = 1;
                context.fillStyle = 'rgba(61,155,53,0.2)';
                context.fill();
                $('.function.przekazywanieDanych').addClass('hovered')
            } else {
                if (test == 0) {
                    canvas.style.cursor = 'default';
                }
                context.fillStyle = 'rgba(61,155,53,0.1)';
                context.fill();
            }
            if ($('.function.przekazywanieDanych').hasClass('active')) {
                context.lineWidth = 3;
                context.strokeStyle = '#000';
                context.fillStyle = 'rgba(61,155,53,1)';
                context.fill();
                context.stroke();
            } else if ($('.function.przekazywanieDanych').hasClass('activated')) {
                context.fillStyle = 'rgba(61,155,53,1)';
                context.fill();
            }
            context.beginPath();
            context.moveTo(59, 145);
            context.lineTo(266, 266);
            context.lineTo(144, 55);
            context.quadraticCurveTo(84, 95, 59, 145);
            context.closePath();
            if (context.isPointInPath(mouseX, mouseY)) {
                canvas.style.cursor = 'pointer';
                test = 1;
                context.fillStyle = 'rgba(190,214,0,0.2)';
                context.fill();
                $('.function.samoobslugaPracownikow').addClass('hovered')
            } else {
                if (test == 0) {
                    canvas.style.cursor = 'default';
                }
                context.fillStyle = 'rgba(190,214,0,0.1)';
                context.fill();
            }
            if ($('.function.samoobslugaPracownikow').hasClass('active')) {
                context.lineWidth = 3;
                context.strokeStyle = '#000';
                context.fillStyle = 'rgba(190,214,0,1)';
                context.fill();
                context.stroke();
            } else if ($('.function.samoobslugaPracownikow').hasClass('activated')) {
                context.fillStyle = 'rgba(190,214,0,1)';
                context.fill();
            }
            context.beginPath();
            context.moveTo(144, 55);
            context.lineTo(266, 266);
            context.lineTo(264, 22);
            context.quadraticCurveTo(200, 18, 144, 55);
            context.closePath();
            if (context.isPointInPath(mouseX, mouseY)) {
                canvas.style.cursor = 'pointer';
                test = 1;
                context.fillStyle = 'rgba(254,224,0,0.2)';
                context.fill();
                $('.function.analizy').addClass('hovered')
            } else {
                if (test == 0) {
                    canvas.style.cursor = 'default';
                }
                context.fillStyle = 'rgba(254,224,0,0.1)';
                context.fill();
            }
            if ($('.function.analizy').hasClass('active')) {
                context.lineWidth = 3;
                context.strokeStyle = '#000';
                context.fillStyle = 'rgba(254,224,0,1)';
                context.fill();
                context.stroke();
            } else if ($('.function.analizy').hasClass('activated')) {
                context.fillStyle = 'rgba(254,224,0,1)';
                context.fill();
            }
        }
    }
})


