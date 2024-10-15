class NavbarManager {
  constructor() {
    this.bodyElement = document.querySelector("body");

    this.sideBarBlurElement = document.getElementById("sidebar-blur");

    this.sideBarContainerElement = document.getElementById("sidebar-container");

    this.sidebarOpenButton = document.getElementById("sidebar-open-button");

    this.sidebarCloseButton = document.getElementById("sidebar-close-button");

    this.signOutButton = document.getElementById("sign-out-button");

    this.init();
  }

  init() {
    document.addEventListener("DOMContentLoaded", () => {
      // Navbar active links
      this.setupActiveLinksStyle();

      // Event listener
      this.setupEventListeners();
    });
  }

  setupActiveLinksStyle() {
    const currentPath = window.location.pathname;

    const linksElement = Array.from(
      document.querySelectorAll("#navbar-links-ul li a")
    ).filter((linkElement) => !linkElement.querySelector("button"));

    linksElement.forEach((linkElement) => {
      const href = linkElement.getAttribute("href");

      if (currentPath === href) {
        linkElement.classList.remove("font-medium", "hover:text-primary");
        linkElement.classList.add("text-primary", "font-semibold");
      }
    });
  }

  setupEventListeners() {
    // Open button (menu button)
    this.sidebarOpenButton.addEventListener("click", () => {
      this.onOpenSidebar();
    });

    // Close button
    this.sidebarCloseButton.addEventListener("click", () => {
      this.onCloseSidebar();
    });

    // Sidebar blur
    this.sideBarBlurElement.addEventListener("click", () => {
      this.onCloseSidebar();
    });

    // Sign out button
    if (this.signOutButton) {
      this.signOutButton.addEventListener("click", () => {
        this.handleSignOut();
      });
    }
  }

  async handleSignOut() {
    // Disable logout button
    this.signOutButton.disabled = true;

    // Call logout API
    try {
      const response = await fetch("/logout", {
        method: "GET",
      });

      const responseBody = await response.json();

      if (!response.ok) {
        throw new Error(responseBody.message);
      }

      // Reload
      window.location.reload();
    } catch (error) {
      alert(error instanceof Error ? error.message : "Unknown error");
    } finally {
      this.signOutButton.disabled = false;
    }
  }

  onOpenSidebar() {
    this.bodyElement.classList.add("body-sidebar-open");

    setTimeout(() => {
      this.sideBarBlurElement.classList.add("sidebar__blur-open");
    }, 100);

    this.sideBarContainerElement.classList.add("sidebar-open");
  }

  onCloseSidebar() {
    this.sideBarContainerElement.classList.remove("sidebar-open");

    setTimeout(() => {
      this.sideBarBlurElement.classList.remove("sidebar__blur-open");
    }, 100);

    this.bodyElement.classList.remove("body-sidebar-open");
  }
}

new NavbarManager();
