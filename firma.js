		let selectedImage = '';
		let backColor = '';

		document.querySelectorAll('.image-button').forEach(button => {
			button.addEventListener('click', () => {
				document.querySelectorAll('.image-button').forEach(btn => btn.classList.remove('selected'));
				button.classList.add('selected');
				selectedImage = button.dataset.img;
				backColor = button.dataset.bcolor;
			});
		});

		function generateSignature() {
			const text1 = document.getElementById('text1').value || "Nombres Apellido1 Apellido2";
			const text2value = document.getElementById('text2').value || "correo@utalca.cl";
			const text3value = document.getElementById('text3').value;
			const text4value = document.getElementById('text4').value;
			const text2 = text4value ? `${text2value}, Anexo: ${text4value}` : text2value;
			const text3 = text3value;
			const text4 = "Instituto de Matemáticas";
			const text5 = "Universidad de Talca, Chile";

			if (!selectedImage) {
				alert("Selecciona un logo primero.");
				return;
			}

			const logo = new Image();
			logo.crossOrigin = "anonymous";
			logo.src = selectedImage;

			logo.onload = () => {
				const logoWidth = 1800;
				const logoHeight = 1080;

				// Medimos los textos para calcular el ancho extra
				const measuringCanvas = document.createElement("canvas");
				const measuringCtx = measuringCanvas.getContext("2d");
				measuringCtx.font = "600 90px 'Work Sans'";
				const width1 = measuringCtx.measureText(text1).width;
				const width2 = measuringCtx.measureText(text2).width;
				measuringCtx.font = "400 80px 'Work Sans'";
				const width3 = measuringCtx.measureText(text3).width;
				const width4 = measuringCtx.measureText(text4).width;

				const maxTextWidth = Math.max(width1, width2, width3, width4);
				const margin = 70;
				const textStartX = logoWidth + margin;
				const totalWidth = textStartX + maxTextWidth + 2 * margin;
				const totalHeight = logoHeight;

				// Crear canvas con nuevo tamaño
				const canvas = document.getElementById('canvas');
				const ctx = canvas.getContext('2d');
				canvas.width = totalWidth;
				canvas.height = totalHeight;

				// Fondo
				if (backColor === 'blanco') {
					ctx.fillStyle = "#ffffff";
					ctx.fillRect(0, 0, totalWidth, totalHeight);
				} else if (backColor === 'negro') {
					ctx.fillStyle = "#000000";
					ctx.fillRect(0, 0, totalWidth, totalHeight);
				} else {
					ctx.clearRect(0, 0, totalWidth, totalHeight);
				}

				// Logo
				ctx.drawImage(logo, 0, 0, logoWidth, logoHeight);

				// Línea vertical
				ctx.fillStyle = getColor(text1, 'line');
				ctx.fillRect(logoWidth - 10, 200, 4, 680);

				// Textos
				ctx.font = "600 90px 'Work Sans'";
				ctx.fillStyle = getColor(text1, 'main');
				ctx.fillText(text1, textStartX, 320);

				ctx.fillStyle = getColor(text1, 'alt');
				ctx.fillText(text2, textStartX, 440);

				ctx.fillStyle = getColor(text1, 'main');
				ctx.fillText(text3, textStartX, 560);

				ctx.font = "400 80px 'Work Sans'";
				ctx.fillStyle = getColor(text1, 'main');
				ctx.fillText(text4, textStartX, 700);
				ctx.fillText(text5, textStartX, 850);

				// Reducción a 25%
				const finalCanvas = document.createElement("canvas");
				finalCanvas.width = totalWidth / 4;
				finalCanvas.height = totalHeight / 4;
				const finalCtx = finalCanvas.getContext("2d");
				finalCtx.drawImage(canvas, 0, 0, finalCanvas.width, finalCanvas.height);

				const dataURL = finalCanvas.toDataURL("image/png");

				const downloadLink = document.getElementById('download-link');
				downloadLink.style.display = "block";
				downloadLink.onclick = () => {
					const a = document.createElement('a');
					a.href = dataURL;
					a.download = "firma.png";
					a.click();
				};

				canvas.style.display = "block";
				canvas.width = finalCanvas.width;
				canvas.height = finalCanvas.height;
				ctx.clearRect(0, 0, canvas.width, canvas.height);
				ctx.drawImage(finalCanvas, 0, 0);
			};
		}


		function getColor(imageName, type) {
			if (selectedImage.includes("blanco")) {
				return "#ffffff";
			} else if (selectedImage.includes("negro")) {
				return "#000000";
			} else {
				if (type === 'main') return "rgb(87, 87, 86)";
				if (type === 'alt') return "rgb(29, 113, 184)";
				if (type === 'line') return "rgb(87, 87, 86)";
			}
		}