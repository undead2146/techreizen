document.addEventListener("DOMContentLoaded", () => {
    const cityInput = document.querySelector('[name="city"]');
    const citySelector = document.querySelector(".city-selector");

    if (cityInput && citySelector) {
        // Handle autofill
        cityInput.addEventListener("animationstart", (e) => {
            if (e.animationName === "onAutoFillStart") {
                handleAutofill(cityInput.value);
            }
        });

        // Handle manual input
        cityInput.addEventListener("input", (e) => {
            handleCitySelection(e.target.value);
        });
    }

    function handleAutofill(value) {
        if (value) {
            // Prevent the selector from immediately disappearing
            setTimeout(() => {
                handleCitySelection(value);
            }, 100);
        }
    }

    function handleCitySelection(value) {
        // Your existing city selection logic here
        // Keep the selector visible until proper selection
        if (value) {
            citySelector.style.display = "block";
        }
    }
});
