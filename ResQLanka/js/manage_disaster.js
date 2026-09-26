
    document.addEventListener("DOMContentLoaded", function() {
    const tabs = document.querySelectorAll(".tab");
                const sections = document.querySelectorAll(".table-section");

                tabs.forEach(tab => {
                    tab.addEventListener("click", function(e) {
                        e.preventDefault(); 

                        tabs.forEach(t => t.classList.remove("active"));
                        
                        this.classList.add("active");

                        const targetId = this.getAttribute("data-tab");

                        if (targetId === "all") {
                            sections.forEach(s => s.style.display = "block");
                        } else {
                            sections.forEach(s => s.style.display = "none");
                            document.getElementById(targetId).style.display = "block";
                        }
                    });
        });
    });
