// ---- Settings: change these if your details change ----
const EMAIL = "amitielysrafelespinosa@gmail.com";
const GMAIL_SUBJECT = "Freelance inquiry";
const GMAIL_BODY = "Hi Amitiel,\n\nI found your portfolio and would like to talk about a project.\n\n";

// ---- Gmail buttons: opens Gmail's compose window with your address filled in ----
const gmailUrl = "https://mail.google.com/mail/?view=cm&fs=1" +
  "&to=" + encodeURIComponent(EMAIL) +
  "&su=" + encodeURIComponent(GMAIL_SUBJECT) +
  "&body=" + encodeURIComponent(GMAIL_BODY);
document.querySelectorAll("#gmail-hero, #gmail-contact").forEach(a => (a.href = gmailUrl));

// ---- Footer year ----
document.getElementById("year").textContent = new Date().getFullYear();

// ---- Mobile menu ----
const menuBtn = document.querySelector(".menu-btn");
const nav = document.getElementById("nav");
menuBtn.addEventListener("click", () => {
  const open = nav.classList.toggle("open");
  menuBtn.setAttribute("aria-expanded", open);
});
nav.addEventListener("click", e => {
  if (e.target.tagName === "A") { nav.classList.remove("open"); menuBtn.setAttribute("aria-expanded", false); }
});

// ---- Contact form: sends to php/contact.php, which saves it in MySQL ----
const form = document.getElementById("contact-form");
const statusEl = document.getElementById("form-status");
form.addEventListener("submit", async e => {
  e.preventDefault();
  statusEl.className = "";
  if (!form.checkValidity()) {
    statusEl.textContent = "Please fill in every field with a valid email.";
    statusEl.className = "err";
    return;
  }
  const btn = form.querySelector("button");
  btn.disabled = true;
  statusEl.textContent = "Sending...";
  try {
    const res = await fetch("php/contact.php", { method: "POST", body: new FormData(form) });
    const data = await res.json();
    statusEl.textContent = data.message;
    statusEl.className = data.ok ? "ok" : "err";
    if (data.ok) form.reset();
  } catch (err) {
    statusEl.textContent = "Could not send. Please use the Gmail button instead.";
    statusEl.className = "err";
  }
  btn.disabled = false;
});
