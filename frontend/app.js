const authPanel = document.getElementById("auth-panel");
const dashboard = document.getElementById("dashboard");
const authForm = document.getElementById("auth-form");
const authMessage = document.getElementById("auth-message");
const productMessage = document.getElementById("product-message");
const productDialog = document.getElementById("product-dialog");
const productForm = document.getElementById("product-form");
const dialogMessage = document.getElementById("dialog-message");
const productList = document.getElementById("product-list");

let authMode = "login";
let currentUser = null;
let products = [];

async function api(path, options = {}) {
  const response = await fetch(`/api/${path}`, {
    credentials: "same-origin",
    ...options,
    headers: {
      Accept: "application/json",
      ...(options.body ? { "Content-Type": "application/json" } : {}),
      ...options.headers,
    },
  });

  let result;
  try {
    result = await response.json();
  } catch {
    throw new Error("The server returned an unreadable response.");
  }

  if (!response.ok || result.success === false) {
    const error = new Error(result.error || result.message || "The request failed.");
    error.status = response.status;
    throw error;
  }
  return result;
}

function showMessage(element, text = "", kind = "") {
  element.textContent = text;
  element.className = `message${kind ? ` ${kind}` : ""}`;
}

function setAuthMode(mode) {
  authMode = mode;
  const signup = mode === "signup";
  document.getElementById("auth-title").textContent = signup ? "Create account" : "Sign in";
  document.getElementById("auth-mode-label").textContent = signup ? "GET STARTED" : "WELCOME BACK";
  document.getElementById("auth-description").textContent = signup
    ? "Create an account to manage your product catalog."
    : "Use your account details to continue.";
  document.getElementById("username-field").classList.toggle("hidden", !signup);
  authForm.elements.username.required = signup;
  authForm.elements.password.autocomplete = signup ? "new-password" : "current-password";
  document.getElementById("auth-submit").textContent = signup ? "Create account" : "Sign in";
  document.getElementById("switch-prompt").textContent = signup ? "Already have an account?" : "New here?";
  document.getElementById("switch-mode").textContent = signup ? "Sign in" : "Create an account";
  showMessage(authMessage);
}

function navigate(path, replace = false) {
  const method = replace ? "replaceState" : "pushState";
  window.history[method]({}, "", path);
}

async function enterDashboard(user) {
  currentUser = user;
  document.getElementById("user-name").textContent = user.username || user.email;
  authPanel.classList.add("hidden");
  dashboard.classList.remove("hidden");
  if (window.location.pathname !== "/products") {
    navigate("/products", true);
  }
  await loadProducts();
}

async function loadProducts() {
  showMessage(productMessage);
  try {
    const result = await api("products");
    products = result.data || [];
    renderProducts();
  } catch (error) {
    showMessage(productMessage, error.message, "error");
    if (error.status === 401) {
      currentUser = null;
      dashboard.classList.add("hidden");
      authPanel.classList.remove("hidden");
    }
  }
}

function renderProducts() {
  productList.replaceChildren();
  document.getElementById("product-count").textContent =
    `${products.length} ${products.length === 1 ? "item" : "items"} in your catalog`;
  document.getElementById("empty-state").classList.toggle("hidden", products.length > 0);

  for (const product of products) {
    const row = document.createElement("tr");
    const nameCell = document.createElement("td");
    nameCell.className = "product-cell";
    const name = document.createElement("strong");
    name.textContent = product.product_name || "";
    const description = document.createElement("small");
    description.textContent = product.description || "No description";
    nameCell.append(name, description);

    const price = document.createElement("td");
    price.textContent = Number(product.price).toLocaleString("en-PH", {
      style: "currency",
      currency: "PHP",
    });
    const quantity = document.createElement("td");
    quantity.textContent = String(product.quantity ?? 0);
    const actions = document.createElement("td");
    const actionGroup = document.createElement("div");
    actionGroup.className = "row-actions";
    const edit = document.createElement("button");
    edit.type = "button";
    edit.className = "small-button";
    edit.textContent = "Edit";
    edit.addEventListener("click", () => openProductDialog(product));
    const remove = document.createElement("button");
    remove.type = "button";
    remove.className = "small-button delete";
    remove.textContent = "Delete";
    remove.addEventListener("click", () => deleteProduct(product.id, remove));
    actionGroup.append(edit, remove);
    actions.append(actionGroup);
    row.append(nameCell, price, quantity, actions);
    productList.append(row);
  }
}

function openProductDialog(product = null) {
  productForm.reset();
  productForm.elements.id.value = product?.id || "";
  document.getElementById("dialog-title").textContent = product ? "Edit product" : "Add a product";
  document.getElementById("save-product").textContent = product ? "Save changes" : "Save product";
  if (product) {
    productForm.elements.product_name.value = product.product_name || "";
    productForm.elements.description.value = product.description || "";
    productForm.elements.price.value = product.price ?? "";
    productForm.elements.quantity.value = product.quantity ?? "";
  }
  showMessage(dialogMessage);
  productDialog.showModal();
}

async function deleteProduct(id, button) {
  if (!window.confirm("Delete this product? This cannot be undone.")) return;
  button.disabled = true;
  try {
    await api(`products/${encodeURIComponent(id)}`, { method: "DELETE" });
    await loadProducts();
  } catch (error) {
    showMessage(productMessage, error.message, "error");
    button.disabled = false;
  }
}

document.getElementById("switch-mode").addEventListener("click", () => {
  const mode = authMode === "login" ? "signup" : "login";
  navigate(mode === "signup" ? "/signup" : "/login");
  setAuthMode(mode);
});

authForm.addEventListener("submit", async (event) => {
  event.preventDefault();
  const submit = document.getElementById("auth-submit");
  submit.disabled = true;
  showMessage(authMessage);

  const formData = new FormData(authForm);
  const payload = Object.fromEntries(formData.entries());
  try {
    if (authMode === "signup") {
      await api("auth/signup", { method: "POST", body: JSON.stringify(payload) });
      authForm.reset();
      navigate("/login");
      setAuthMode("login");
      showMessage(authMessage, "Account created. Sign in to continue.", "success");
      return;
    }

    const result = await api("auth/login", { method: "POST", body: JSON.stringify(payload) });
    await enterDashboard(result.data);
  } catch (error) {
    showMessage(authMessage, error.message, "error");
  } finally {
    submit.disabled = false;
  }
});

productForm.addEventListener("submit", async (event) => {
  event.preventDefault();
  const save = document.getElementById("save-product");
  save.disabled = true;
  showMessage(dialogMessage);
  const values = Object.fromEntries(new FormData(productForm).entries());
  const id = values.id;
  delete values.id;
  try {
    await api(id ? `products/${encodeURIComponent(id)}` : "products", {
      method: id ? "PUT" : "POST",
      body: JSON.stringify(values),
    });
    productDialog.close();
    await loadProducts();
  } catch (error) {
    showMessage(dialogMessage, error.message, "error");
  } finally {
    save.disabled = false;
  }
});

document.getElementById("add-product").addEventListener("click", () => openProductDialog());
document.getElementById("close-dialog").addEventListener("click", () => productDialog.close());
document.getElementById("cancel-product").addEventListener("click", () => productDialog.close());

document.getElementById("logout-button").addEventListener("click", async () => {
  try {
    await api("auth/logout", { method: "POST", body: "{}" });
    currentUser = null;
    dashboard.classList.add("hidden");
    authPanel.classList.remove("hidden");
    authForm.reset();
    navigate("/login");
    setAuthMode("login");
  } catch (error) {
    showMessage(productMessage, error.message, "error");
  }
});

async function restoreSession() {
  try {
    const result = await api("auth/session");
    await enterDashboard(result.data);
  } catch (error) {
    const path = window.location.pathname;
    if (path === "/products") {
      navigate("/login", true);
    }
    setAuthMode(path === "/signup" ? "signup" : "login");
    if (error.status !== 401) {
      showMessage(authMessage, error.message, "error");
    }
  }
}

window.addEventListener("popstate", () => {
  const path = window.location.pathname;
  if (path === "/products") {
    restoreSession();
    return;
  }

  dashboard.classList.add("hidden");
  authPanel.classList.remove("hidden");
  setAuthMode(path === "/signup" ? "signup" : "login");
});

restoreSession();
