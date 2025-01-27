import cv2
import sys

image_path = sys.argv[1]

# Carregar a imagem
image = cv2.imread(image_path)

# Inicializar o detector de QR Code
detector = cv2.QRCodeDetector()

# Detectar e decodificar o QR Code
data, bbox, _ = detector.detectAndDecode(image)

if data:
    print(data)
else:
    print("Nenhum QR Code detectado.")
