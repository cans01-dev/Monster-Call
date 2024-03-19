import { Toast } from "bootstrap";

const toastElList = document.querySelectorAll(".toast");
[...toastElList].map((toastEl) => {
  const toast = new Toast(toastEl);
  toast.show();
});