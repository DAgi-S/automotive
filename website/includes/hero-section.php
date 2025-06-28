<?php
// Get current page for conditional styling
$current_page = basename($_SERVER['PHP_SELF']);
// Car images for the slider
$car_images = [
    'swift.png',
    'das.png',
    'byd.png',
    'hilux.png',
    'tacoma.png',
    'esperso.png',
    'dzire.png',
    'engin.png',
];
?>
<section class="hero-section">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="hero-slider-area">
            <div class="car-slider">
                <button class="slider-arrow left" onclick="prevCar()"><i class="fas fa-chevron-left"></i></button>
                <img id="mainCarImage" src="assets/images/cars/<?php echo $car_images[0]; ?>" alt="Car" class="main-car-img" />
                <button class="slider-arrow right" onclick="nextCar()"><i class="fas fa-chevron-right"></i></button>
            </div>
            <div class="car-thumbnails">
                <?php foreach ($car_images as $idx => $img): ?>
                    <img src="assets/images/cars/<?php echo $img; ?>" alt="Car Thumbnail" class="car-thumb<?php echo $idx === 0 ? ' active' : ''; ?>" onclick="selectCar(<?php echo $idx; ?>)" />
                <?php endforeach; ?>
            </div>
        </div>
        <div class="hero-text">
            <h1>
                <div class="title-line">Drive <span class="highlight">Better</span>.</div>
                <div class="title-line">Drive <span class="highlight">Protected</span>.</div>
            </h1>
            <p>Expert Car Repair & Maintenance Services You Can Trust.</p>
            <a href="contact.php" class="cta-button">Book a Service</a>
        </div>
    </div>
</section>

<script>
const carImages = <?php echo json_encode($car_images); ?>;
let currentCar = 0;
const mainCarImage = document.getElementById('mainCarImage');
const thumbnails = document.getElementsByClassName('car-thumb');

function updateCarSlider(idx) {
    currentCar = idx;
    mainCarImage.src = 'assets/images/cars/' + carImages[currentCar];
    for (let i = 0; i < thumbnails.length; i++) {
        thumbnails[i].classList.remove('active');
    }
    thumbnails[currentCar].classList.add('active');
}
function prevCar() {
    let idx = currentCar - 1;
    if (idx < 0) idx = carImages.length - 1;
    updateCarSlider(idx);
}
function nextCar() {
    let idx = currentCar + 1;
    if (idx >= carImages.length) idx = 0;
    updateCarSlider(idx);
}
function selectCar(idx) {
    updateCarSlider(idx);
}
</script>

<style>
.hero-section {
    position: relative;
    min-height: 100vh;
    background: linear-gradient(to bottom right, #000000, #1a2634);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.hero-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
    width: 100%;
    align-items: center;
}

.hero-slider-area {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.car-slider {
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    margin-bottom: 1.5rem;
}

.main-car-img {
    width: 350px;
    height: auto;
    max-width: 90vw;
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.4);
    background: #181818;
    object-fit: contain;
    transition: box-shadow 0.3s;
}

.slider-arrow {
    background: #ffbe00;
    border: none;
    color: #222;
    font-size: 2rem;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    margin: 0 10px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    touch-action: manipulation;
}
.slider-arrow:hover, .slider-arrow:active {
    background: #ffd84d;
}

.car-thumbnails {
    display: flex;
    gap: 12px;
    margin-top: 0.5rem;
    justify-content: center;
    flex-wrap: wrap;
}

.car-thumb {
    width: 60px;
    height: 40px;
    object-fit: contain;
    border-radius: 8px;
    border: 2px solid transparent;
    cursor: pointer;
    opacity: 0.7;
    transition: border 0.2s, opacity 0.2s;
    background: #222;
    margin-bottom: 4px;
    touch-action: manipulation;
}
.car-thumb.active, .car-thumb:hover, .car-thumb:active {
    border: 2px solid #ffbe00;
    opacity: 1;
}

.hero-text {
    color: white;
    padding: 2rem;
    text-align: left;
}

.title-line {
    font-size: 3rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.highlight {
    color: #ffbe00;
}

.cta-button {
    font-size: 1.1rem;
    padding: 0.9rem 2.2rem;
    border-radius: 8px;
    margin-top: 1.5rem;
    display: inline-block;
    text-align: center;
}

@media (max-width: 900px) {
    .main-car-img {
        width: 250px;
    }
    .title-line {
        font-size: 2rem;
    }
}

@media (max-width: 768px) {
    .hero-content {
        grid-template-columns: 1fr;
        gap: 1.5rem;
        padding: 1rem;
    }
    .hero-slider-area {
        margin-bottom: 1.5rem;
    }
    .main-car-img {
        width: 90vw;
        max-width: 350px;
    }
    .hero-text {
        padding: 1rem 0.5rem;
        text-align: center;
    }
    .title-line {
        font-size: 1.5rem;
    }
    .cta-button {
        font-size: 1rem;
        padding: 0.8rem 1.5rem;
        margin-top: 1rem;
    }
    .car-thumbnails {
        gap: 8px;
    }
    .car-thumb {
        width: 44px;
        height: 28px;
    }
}

@media (max-width: 480px) {
    .main-car-img {
        width: 98vw;
        max-width: 98vw;
    }
    .hero-content {
        padding: 0.5rem;
    }
    .hero-text {
        padding: 0.5rem 0.2rem;
    }
    .title-line {
        font-size: 1.1rem;
    }
    .cta-button {
        font-size: 0.95rem;
        padding: 0.7rem 1.1rem;
    }
    .car-thumb {
        width: 32px;
        height: 20px;
    }
}
</style> 