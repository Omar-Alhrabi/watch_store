document.addEventListener("DOMContentLoaded", () => {
    const priceRange = document.getElementById("priceRange");
    const priceValue = document.getElementById("priceValue");
    const filterBrand = document.getElementById("filter-brand");
    const filterColor  = document.getElementById("filter-color");
    const featureChrono = document.getElementById("feature-chrono");
    const featureAuto = document.getElementById("feature-auto");
    const featureWaterproof = document.getElementById("feature-waterproof");
    const resetFilters = document.getElementById("resetFilters");
    const productCards = document.querySelectorAll('.card');

    priceRange.addEventListener("input", () => {
        priceValue.textContent = `$${priceRange.value}`;
        filterProducts();
    });

    function filterProducts() {
        const selectedBrand = filterBrand.value;
        const selectedColor  = filterColor.value;
        const selectedPrice = parseInt(priceRange.value);
        const selectedFeatures = [];
        
        if (featureChrono.checked) selectedFeatures.push("Chronograph");
        if (featureAuto.checked) selectedFeatures.push("Automatic");
        if (featureWaterproof.checked) selectedFeatures.push("Waterproof");


        console.log("Selected color:", selectedColor);
        
        productCards.forEach(card => {
            const color = card.getAttribute('data-color');
            console.log("Card color:", color);
        });


        productCards.forEach(card => {
            const brand = card.getAttribute('data-brand');
            const price = parseFloat(card.getAttribute('data-price'));
            const features = card.getAttribute('data-features');
            const Color = card.getAttribute('data-Color');
            
            let showCard = true;
            
            if (selectedBrand !== 'all' && brand !== selectedBrand) {
                showCard = false;
            }
            
            if (selectedColor !== 'all' && Color !== selectedColor) {
                showCard = false;
            }
            
            if (price > selectedPrice) {
                showCard = false;
            }
            
            if (selectedFeatures.length > 0) {
                selectedFeatures.forEach(feature => {
                    if (!features.includes(feature)) {
                        showCard = false;
                    }
                });
            }
            card.style.display = showCard ? 'block' : 'none';
        });
    }

    resetFilters.addEventListener("click", () => {
        filterBrand.value = "all";
        filterColor.value = "all";
        priceRange.value = 2500;
        priceValue.textContent = "$2500";
        featureChrono.checked = false;
        featureAuto.checked = false;
        featureWaterproof.checked = false;

        productCards.forEach(card => {
            card.style.display = 'block';
        });
    });

    filterBrand.addEventListener("change", filterProducts);
    filterColor.addEventListener("change", filterProducts);
    featureChrono.addEventListener("change", filterProducts);
    featureAuto.addEventListener("change", filterProducts);
    featureWaterproof.addEventListener("change", filterProducts);

});