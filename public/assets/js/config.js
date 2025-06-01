"use strict";

var base = {
    defaultFontFamily: "Overpass, sans-serif",
    primaryColor: "#1b68ff",
    secondaryColor: "#4f4f4f",
    successColor: "#3ad29f",
    warningColor: "#ffc107",
    infoColor: "#17a2b8",
    dangerColor: "#dc3545",
    darkColor: "#343a40",
    lightColor: "#f2f3f6"
};

var extend = {
    primaryColorLight: tinycolor(base.primaryColor).lighten(10).toString(),
    primaryColorLighter: tinycolor(base.primaryColor).lighten(30).toString(),
    primaryColorDark: tinycolor(base.primaryColor).darken(10).toString(),
    primaryColorDarker: tinycolor(base.primaryColor).darken(30).toString()
};

var chartColors = [base.primaryColor, base.successColor, "#6f42c1", extend.primaryColorLighter];

var colors = {
    bodyColor: "#6c757d",
    headingColor: "#495057",
    borderColor: "#e9ecef",
    backgroundColor: "#f8f9fa",
    mutedColor: "#adb5bd",
    chartTheme: "light"
};

var darkColor = {
    bodyColor: "#adb5bd",
    headingColor: "#e9ecef",
    borderColor: "#212529",
    backgroundColor: "#495057",
    mutedColor: "#adb5bd",
    chartTheme: "dark"
};

// Variabel untuk menyimpan tema saat ini dari localStorage
var currentTheme = localStorage.getItem("mode");

// Dapatkan referensi ke elemen CSS light/dark theme
var dark = document.querySelector("#darkTheme");
var light = document.querySelector("#lightTheme");

// Dapatkan referensi ke elemen mode switcher
var switcher = document.querySelector("#modeSwitcher"); // Mencari elemen dengan ID 'modeSwitcher'

// Fungsi untuk beralih mode
function modeSwitch() {
    console.log("modeSwitch function called"); // Debugging
    var o = localStorage.getItem("mode");
    if (o) {
        if ("dark" === o) { // Gunakan '===' untuk perbandingan ketat
            if (dark) dark.disabled = true; // Nonaktifkan dark theme
            if (light) light.disabled = false; // Aktifkan light theme
            localStorage.setItem("mode", "light");
        } else { // Berarti "light"
            if (dark) dark.disabled = false; // Aktifkan dark theme
            if (light) light.disabled = true; // Nonaktifkan light theme
            localStorage.setItem("mode", "dark");
        }
    } else { // Jika belum ada mode di localStorage
        if ($("body").hasClass("dark")) {
            if (dark) dark.disabled = false;
            if (light) light.disabled = true;
            localStorage.setItem("mode", "dark");
        } else {
            if (dark) dark.disabled = true;
            if (light) light.disabled = false;
            localStorage.setItem("mode", "light");
        }
    }
    // Update data-mode pada switcher setelah mode beralih
    if (switcher) {
        switcher.dataset.mode = localStorage.getItem("mode");
        // Anda juga bisa mengganti ikon di sini berdasarkan mode
        if (localStorage.getItem("mode") === "dark") {
             switcher.innerHTML = '<span class="fe fe-sun" style="font-size: 1.2em;"></span>'; // Ikon matahari
        } else {
             switcher.innerHTML = '<span class="fe fe-moon" style="font-size: 1.2em;"></span>'; // Ikon bulan
        }
    }
}

// Tambahkan event listener ke switcher jika ada
// if (switcher) {
//     switcher.addEventListener('click', modeSwitch);
//     console.log("#modeSwitcher found and event listener added."); // Debugging
// } else {
//     console.warn("Elemen dengan ID 'modeSwitcher' tidak ditemukan. Mode switch tidak akan berfungsi."); // Debugging
// }


console.log("Current Theme from localStorage: " + currentTheme); // Debugging
// Terapkan tema saat pemuatan halaman berdasarkan nilai dari localStorage
if (currentTheme) {
    if ("dark" === currentTheme) {
        if (dark) dark.disabled = false; // Aktifkan dark theme
        if (light) light.disabled = true;  // Nonaktifkan light theme
        colors = darkColor; // Gunakan skema warna gelap
    } else { // Berarti "light"
        if (dark) dark.disabled = true;   // Nonaktifkan dark theme
        if (light) light.disabled = false; // Aktifkan light theme
        // colors = lightColor; // colors default sudah lightColor, jadi tidak perlu diubah
    }
    // Set data-mode pada elemen switcher jika ditemukan
    if (switcher) {
        switcher.dataset.mode = currentTheme;
        // Sesuaikan ikon awal saat halaman dimuat
        if (currentTheme === "dark") {
             switcher.innerHTML = '<span class="fe fe-sun" style="font-size: 1.2em;"></span>';
        } else {
             switcher.innerHTML = '<span class="fe fe-moon" style="font-size: 1.2em;"></span>';
        }
    }
} else { // Jika belum ada mode di localStorage saat pertama kali load
    if ($("body").hasClass("dark")) { // Cek apakah body memiliki kelas 'dark'
        colors = darkColor;
        localStorage.setItem("mode", "dark");
        if (dark) dark.disabled = false;
        if (light) light.disabled = true;
    } else {
        localStorage.setItem("mode", "light");
        if (dark) dark.disabled = true;
        if (light) light.disabled = false;
    }
    // Set data-mode pada elemen switcher saat pertama kali load
    if (switcher) {
        switcher.dataset.mode = $("body").hasClass("dark") ? "dark" : "light";
        if ($("body").hasClass("dark")) {
             switcher.innerHTML = '<span class="fe fe-sun" style="font-size: 1.2em;"></span>';
        } else {
             switcher.innerHTML = '<span class="fe fe-moon" style="font-size: 1.2em;"></span>';
        }
    }
}