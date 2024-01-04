var AddressSearch = {
    create: function (inputElementId, outputElementId) {
        const addressElement = document.getElementById(inputElementId);
        if (addressElement)
            addressElement.addEventListener("input", (e) =>
                fetchAddress(e.target.value)
            );
        if (addressElement)
            addressElement.addEventListener(
                "focus",
                displayAddressSearchResult
            );

        let searchAddressResults = [];
        const searchResultElement = document.getElementById(outputElementId);

        function onOutsideClickAddress(e) {
            if (e.target === addressElement) return;
            document.removeEventListener("click", onOutsideClickAddress);
            if (searchResultElement) searchResultElement.style.display = "none";
        }

        async function onAddressClick(center) {
            addressElement.value = center.place_name;

            //create hidden input field with the lat and lng
            var latInput = `<input type='hidden' name='latitude' value='${center.center[1]}'>`;
            var longInput = `<input type='hidden' name='longitude' value='${center.center[0]}'>`;

            //remove lat and lng input field if it already exists
            var prevlatInput = $('input[name="latitude"');
            if (prevlatInput) {
                prevlatInput.remove();
            }

            var prevlongInput = $('input[name="longitude"');
            if (prevlongInput) {
                prevlongInput.remove();
            }

            //add the new lat and lng input field
            addressElement.insertAdjacentHTML('afterend', latInput);
            addressElement.insertAdjacentHTML('afterend', longInput);
       
        }

        function displayAddressSearchResult() {
            searchResultElement.addEventListener;
            if (searchResultElement)
                searchResultElement.style.display = "block";

            const cityMenu = document.getElementById("cityMenu");
            if (cityMenu) cityMenu.remove();

            const list = document.createElement("ul");
            list.className = "list-group";
            list.id = "cityMenu";

            if (searchResultElement) searchResultElement.appendChild(list);

            searchAddressResults.forEach((result) => {
                if (result.place_type == 'address') {
                    const item = document.createElement("li");
                    item.className = "list-group-item";
                    item.innerText = result.place_name;
                    item.addEventListener("click", () => onAddressClick(result));
                    list.appendChild(item);
                }
            });
            document.addEventListener("click", onOutsideClickAddress);
        }
        
        let fetchAddressTimeout;
        let fetchAddressController;

        const mapToken = "pk.eyJ1Ijoic3VubnlzYW53YXIiLCJhIjoiY2wwNjV5N3kzMDQwbTNib2NhMnd6NGg2dCJ9.501q9aEzAkIe4RzQm-IzQg";
        
        const fetchAddress = (value) => {
            const url = `https://api.mapbox.com/geocoding/v5/mapbox.places/${value}.json?country=US&access_token=${mapToken}`;

            if (fetchAddressController) fetchAddressController.abort();
            clearTimeout(fetchAddressTimeout);
            if (!value) {
                searchAddressResults = [];
                displayAddressSearchResult();
                return;
            }
            fetchAddressTimeout = setTimeout(() => {
                fetchAddressController = new AbortController();
                fetch(url, {
                    signal: fetchAddressController.signal,
                })
                    .then((res) => res.json())
                    .then((data) => {
                        searchAddressResults = data.features;
                        console.log(searchAddressResults);
                        displayAddressSearchResult();
                    });
            }, 200);
        };
    },
};