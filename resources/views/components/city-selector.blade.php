@props(['selectedCity' => null, 'selectedPostalCode' => null, 'hasError' => false])

<div
    x-data="citySelectorComponent()"
    x-init="initialize('{{ $selectedCity }}', '{{ $selectedPostalCode }}')"
    class="premium-city-selector"
>
    <div class="search-container">
        <div class="input-container">
            <div class="input-wrapper">
                <span class="search-icon">
                    <i class="fas fa-search"></i>
                </span>
                <input
                    type="text"
                    class="search-input {{ $hasError ? 'is-invalid' : '' }}"
                    placeholder="Zoek gemeente of postcode..."
                    x-model="searchQuery"
                    x-on:input="searchCities"
                    x-on:focus="showDropdown = true"
                    x-on:keydown.arrow-down.prevent="navigateDown()"
                    x-on:keydown.arrow-up.prevent="navigateUp()"
                    x-on:keydown.enter.prevent="selectHighlighted()"
                    x-on:keydown.escape="clearSearch()"
                    autocomplete="off"
                >
                <span class="search-actions">
                    <button
                        x-show="searchQuery && searchQuery.length > 0"
                        @click="clearSearch()"
                        type="button"
                        class="clear-btn"
                        title="Wissen"
                    >
                        <i class="fas fa-times-circle"></i>
                    </button>
                </span>
            </div>
        </div>

        <input type="hidden" name="city" x-model="selectedCity" {{ $attributes }}>
        <input type="hidden" name="postcode" x-model="selectedPostalCode">

        <div class="city-selection" x-show="selectedCity || selectedPostalCode" x-transition>
            <div class="selection-badge">
                <span class="postal-code" x-show="selectedPostalCode" x-text="selectedPostalCode"></span>
                <span class="city-name" x-show="selectedCity" x-text="selectedCity"></span>
                <button type="button" class="remove-selection" @click="clearSelection()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Dropdown -->
        <div
            class="search-results-dropdown"
            x-show="showDropdown && searchQuery && searchQuery.length >= 2"
            x-cloak
        >
            <template x-if="isLoading">
                <div class="loading-indicator">
                    <div class="spinner-border text-primary spinner-border-sm" role="status">
                        <span class="visually-hidden">Laden...</span>
                    </div>
                    <span>Gemeenten laden...</span>
                </div>
            </template>
            <template x-if="!isLoading && searchQuery && searchQuery.length >= 2 && searchResults.length === 0">
                <div class="no-results">
                    <div class="no-results-message">
                        <i class="fas fa-map-marker-alt"></i>
                        <p>Geen gemeenten gevonden voor "<span x-text="searchQuery"></span>"</p>
                    </div>
                    <button type="button" class="create-new-btn" @click.prevent="showAddCityModal = true">
                        <i class="fas fa-plus-circle"></i> Nieuwe gemeente toevoegen
                    </button>
                </div>
            </template>
            <ul class="results-list" x-show="searchResults.length > 0">
                <template x-for="(city, index) in searchResults" :key="index">
                    <li
                        class="result-item"
                        :class="{ 'highlighted': highlightedIndex === index }"
                        @mouseover="highlightedIndex = index"
                        @click="selectCity(city)"
                    >
                        <div class="result-icon">
                            <i class="fas fa-city"></i>
                        </div>
                        <div class="result-details">
                            <span class="result-name" x-text="city.plaatsnaam"></span>
                            <span class="result-postal" x-show="city.postcode" x-text="city.postcode"></span>
                        </div>
                    </li>
                </template>
            </ul>
        </div>
    </div>

    <!-- Add City Modal -->
    <div
        class="city-modal-overlay"
        x-show="showAddCityModal"
        x-transition
        x-cloak
    >
        <div
            class="city-modal-container"
            @click.away="showAddCityModal = false"
        >
            <div class="city-modal-header">
                <h3 class="modal-title">
                    <i class="fas fa-city"></i>
                    Nieuwe gemeente toevoegen
                </h3>
                <button type="button" class="modal-close" @click="showAddCityModal = false">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="city-modal-body">
                <div class="form-group">
                    <label for="new-city-name">Naam gemeente <span class="required">*</span></label>
                    <input
                        type="text"
                        id="new-city-name"
                        name="new_city_name"
                        class="form-control"
                        x-model="newCityName"
                        placeholder="Voer gemeentenaam in"
                    >
                </div>
                <div class="form-group">
                    <label for="new-postal-code">Postcode <span class="required">*</span></label>
                    <input
                        type="text"
                        id="new-postal-code"
                        name="new_postcode"
                        class="form-control"
                        x-model="newPostalCode"
                        placeholder="Voer postcode in"
                    >
                    <small class="form-text text-muted">Bijv. 3000</small>
                </div>
                <div class="form-group">
                    <label for="new-province">Provincie <span class="optional">(optioneel)</span></label>
                    <input
                        type="text"
                        id="new-province"
                        name="new_province"
                        class="form-control"
                        x-model="newProvince"
                        placeholder="Voer provincie in"
                    >
                </div>
                <div class="error-message" x-show="errorMessage" x-text="errorMessage"></div>
            </div>
            <div class="city-modal-footer">
                <button type="button" class="btn-cancel" @click="showAddCityModal = false">
                    Annuleren
                </button>
                <button
                    type="button"
                    class="btn-save"
                    @click="addNewCity()"
                    :disabled="!newCityName || newCityName.trim().length < 2 || !newPostalCode || newPostalCode.trim().length < 1"
                >
                    <i class="fas fa-save"></i>
                    Gemeente opslaan
                </button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/city-selector.js') }}"></script>

<style>
/* Premium City Selector Styling */
.premium-city-selector {
    position: relative;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
}

/* Search Container */
.search-container {
    position: relative;
}

/* Input Container */
.input-container {
    position: relative;
    margin-bottom: 0.5rem;
}

.input-wrapper {
    display: flex;
    align-items: center;
    background-color: #fff;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    transition: all 0.2s ease;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    overflow: hidden;
}

.input-wrapper:focus-within {
    border-color: #4361ee;
    box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
}

.search-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 0.75rem;
    color: #6c757d;
}

.search-input {
    flex: 1;
    border: none;
    padding: 0.75rem 0.5rem;
    font-size: 1rem;
    outline: none;
    width: 100%;
    background: transparent;
}

.search-actions {
    display: flex;
    align-items: center;
}

.clear-btn {
    background: none;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem;
    color: #6c757d;
    cursor: pointer;
    transition: color 0.2s ease;
}

.clear-btn:hover {
    color: #dc3545;
}

/* Selection Badge */
.city-selection {
    margin-top: 0.5rem;
}

.selection-badge {
    display: inline-flex;
    align-items: center;
    background-color: #e7f0ff;
    border: 1px solid #c3d7ff;
    border-radius: 30px;
    padding: 0.4rem 0.8rem;
    font-size: 0.9rem;
    color: #0d47a1;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    transition: all 0.2s ease;
    margin-bottom: 0.25rem;
}

.selection-badge:hover {
    background-color: #d8e7ff;
}

.postal-code {
    font-weight: 600;
    margin-right: 0.25rem;
}

.postal-code:after {
    content: " - ";
    font-weight: normal;
    color: #6c757d;
}

.city-name {
    margin-right: 0.5rem;
}

.remove-selection {
    background: none;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    cursor: pointer;
    padding: 0.15rem;
    border-radius: 50%;
    font-size: 0.8rem;
    transition: all 0.2s ease;
}

.remove-selection:hover {
    background-color: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

/* Enhanced Dropdown */
.search-results-dropdown {
    position: absolute;
    width: 100%;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: 9999;
    max-height: 400px;
    overflow-y: auto;
    border: 1px solid #e9ecef;
    margin-top: 4px;
}

/* Loading Indicator */
.loading-indicator {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
    color: #6c757d;
    gap: 0.5rem;
}

/* No Results */
.no-results {
    padding: 1.5rem;
    text-align: center;
}

.no-results-message {
    margin-bottom: 1rem;
    color: #6c757d;
}

.no-results-message i {
    font-size: 1.5rem;
    color: #adb5bd;
    margin-bottom: 0.5rem;
}

.create-new-btn {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.create-new-btn:hover {
    background-color: #e9ecef;
    border-color: #dee2e6;
}

/* Results List */
.results-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

.result-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    cursor: pointer;
    border-bottom: 1px solid #f8f9fa;
    transition: background-color 0.15s ease;
}

.result-item:last-child {
    border-bottom: none;
}

.result-item:hover, .result-item.highlighted {
    background-color: #f8f9fa;
}

.result-icon {
    margin-right: 1rem;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #e7f0ff;
    color: #4361ee;
    border-radius: 50%;
    flex-shrink: 0;
}

.result-details {
    display: flex;
    flex-direction: column;
}

.result-name {
    font-weight: 500;
    margin-bottom: 0.1rem;
}

.result-postal {
    font-size: 0.8rem;
    color: #6c757d;
}

/* Modal Styling */
.city-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1050;
    padding: 1rem;
}

.city-modal-container {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    width: 100%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
    animation: modal-appear 0.3s ease;
}

@keyframes modal-appear {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.city-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e9ecef;
}

.modal-title {
    margin: 0;
    font-size: 1.25rem;
    color: #343a40;
    display: flex;
    align-items: center;
}

.modal-title i {
    margin-right: 0.5rem;
    color: #4361ee;
}

.modal-close {
    background: none;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.modal-close:hover {
    background-color: #f8f9fa;
    color: #343a40;
}

.city-modal-body {
    padding: 1.5rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #495057;
}

.required {
    color: #dc3545;
}

.optional {
    color: #6c757d;
    font-weight: normal;
    font-size: 0.9rem;
}

.form-control {
    display: block;
    width: 100%;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: 6px;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control:focus {
    border-color: #4361ee;
    box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
    outline: 0;
}

.form-hint {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.85rem;
    color: #6c757d;
}

.error-message {
    color: #dc3545;
    background-color: #f8d7da;
    padding: 0.75rem 1rem;
    border-radius: 4px;
    margin-top: 1rem;
    font-size: 0.9rem;
}

.city-modal-footer {
    padding: 1rem 1.5rem;
    background-color: #f8f9fa;
    border-top: 1px solid #e9ecef;
    display: flex;
    justify-content: flex-end;
    gap: 0.75rem;
    border-bottom-left-radius: 8px;
    border-bottom-right-radius: 8px;
}

.btn-cancel, .btn-save {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-cancel {
    background-color: #fff;
    border: 1px solid #ced4da;
    color: #343a40;
}

.btn-cancel:hover {
    background-color: #e9ecef;
}

.btn-save {
    background-color: #4361ee;
    border: 1px solid #4361ee;
    color: white;
    display: flex;
    align-items: center;
}

.btn-save:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-save:not(:disabled):hover {
    background-color: #3a56d5;
}

.btn-save i {
    margin-right: 0.5rem;
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .city-modal-container {
        width: 95%;
    }
}
</style>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('citySelectorComponent', () => ({
        searchQuery: '',
        selectedCity: '',
        selectedPostalCode: '',
        newCityName: '',
        newPostalCode: '',
        newProvince: '',
        searchResults: [],
        showDropdown: false,
        showAddCityModal: false,
        errorMessage: '',
        isLoading: false,
        highlightedIndex: -1,
        debounceTimer: null,

        initialize(selectedCity, selectedPostalCode) {
            // Set initial values - ensure we have strings, not undefined or "null"
            this.selectedCity = selectedCity && selectedCity !== 'null' ? selectedCity : '';
            this.selectedPostalCode = selectedPostalCode && selectedPostalCode !== 'null' ? selectedPostalCode : '';
            this.searchQuery = this.selectedCity || '';

            // If we have initial values, trigger a search to validate/select the city
            if (this.searchQuery) {
                this.$nextTick(() => {
                    this.performSearch();
                });
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!this.$el.contains(e.target)) this.showDropdown = false;
            });
        },

        searchCities() {
            if (this.debounceTimer) clearTimeout(this.debounceTimer);

            // Clear results if query is too short
            if (!this.searchQuery || this.searchQuery.length < 2) {
                this.searchResults = [];
                this.showDropdown = false;
                return;
            }

            this.isLoading = true;
            this.debounceTimer = setTimeout(() => this.performSearch(), 300);
        },

        async performSearch() {
            try {
                const response = await fetch(`/api/cities/search?query=${encodeURIComponent(this.searchQuery)}`);
                if (response.ok) {
                    const data = await response.json();
                    this.searchResults = data;

                    // Check for exact match (case-insensitive)
                    const exactMatch = data.find(city =>
                        city.plaatsnaam.toLowerCase() === this.searchQuery.toLowerCase() ||
                        city.postcode === this.searchQuery
                    );

                    if (exactMatch) {
                        // Automatically select the exact match
                        this.selectCity(exactMatch);
                        this.showDropdown = false;
                    } else {
                        this.showDropdown = true;
                        this.highlightedIndex = 0;
                    }
                } else {
                    this.searchResults = [];
                    this.showDropdown = false;
                }
            } catch (error) {
                console.error('Search error:', error);
                this.searchResults = [];
                this.showDropdown = false;
            } finally {
                this.isLoading = false;
            }
        },

        selectCity(city) {
            if (!city) return;

            this.selectedCity = city.plaatsnaam;
            this.selectedPostalCode = city.postcode || '';
            this.searchQuery = city.plaatsnaam;
            this.showDropdown = false;
            this.searchResults = [];  // Clear results after selection

            // Update hidden inputs and trigger events
            this.$nextTick(() => {
                const cityInput = this.$el.querySelector('input[name="city"]');
                const postalInput = this.$el.querySelector('input[name="postcode"]');

                if (cityInput) {
                    cityInput.value = this.selectedCity;
                    cityInput.dispatchEvent(new Event('input', { bubbles: true }));
                    cityInput.dispatchEvent(new Event('change', { bubbles: true }));
                }

                if (postalInput) {
                    postalInput.value = this.selectedPostalCode;
                    postalInput.dispatchEvent(new Event('input', { bubbles: true }));
                    postalInput.dispatchEvent(new Event('change', { bubbles: true }));
                }

                // Dispatch selection event
                this.$dispatch('city-selected', {
                    city: city,
                    selected: true,
                    timestamp: new Date().getTime()
                });
            });
        },

        clearSelection() {
            this.selectedCity = '';
            this.selectedPostalCode = '';
            this.searchQuery = '';

            // Force update hidden inputs
            this.$nextTick(() => {
                const cityInput = this.$el.querySelector('input[name="city"]');
                const postalInput = this.$el.querySelector('input[name="postcode"]');

                if (cityInput) {
                    cityInput.value = '';
                    cityInput.dispatchEvent(new Event('input', { bubbles: true }));
                    cityInput.dispatchEvent(new Event('change', { bubbles: true }));
                }

                if (postalInput) {
                    postalInput.value = '';
                    postalInput.dispatchEvent(new Event('input', { bubbles: true }));
                    postalInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });

            this.$dispatch('city-cleared');
        },

        clearSearch() {
            this.searchQuery = '';
            this.searchResults = [];
            this.showDropdown = false;
        },

        navigateDown() {
            if (!this.showDropdown) return;
            if (this.highlightedIndex < this.searchResults.length - 1) this.highlightedIndex++;
        },

        navigateUp() {
            if (!this.showDropdown) return;
            if (this.highlightedIndex > 0) this.highlightedIndex--;
        },

        selectHighlighted() {
            if (this.showDropdown && this.highlightedIndex >= 0 && this.highlightedIndex < this.searchResults.length) {
                this.selectCity(this.searchResults[this.highlightedIndex]);
            }
        },

        async addNewCity() {
            this.errorMessage = '';

            if (!this.newCityName || this.newCityName.trim().length < 2) {
                this.errorMessage = 'Geef een geldige gemeentenaam op (minimaal 2 tekens)';
                return;
            }

            if (!this.newPostalCode || this.newPostalCode.trim().length < 1) {
                this.errorMessage = 'Geef een geldige postcode op';
                return;
            }

            try {
                const response = await fetch('/api/cities', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        plaatsnaam: this.newCityName.trim(),
                        postcode: this.newPostalCode.trim(),
                        provincie: this.newProvince.trim() || null
                    })
                });

                if (response.ok) {
                    const newCity = await response.json();

                    this.selectCity(newCity);
                    this.showAddCityModal = false;
                    this.newCityName = '';
                    this.newPostalCode = '';
                    this.newProvince = '';

                    // Dispatch appropriate event based on status code
                    const isNewlyCreated = response.status === 201;
                    if (isNewlyCreated) {
                        window.dispatchEvent(new CustomEvent('city-added', {
                            detail: { city: newCity }
                        }));
                    } else {
                        window.dispatchEvent(new CustomEvent('city-found', {
                            detail: { city: newCity }
                        }));
                    }
                } else {
                    const errorData = await response.json();
                    console.error('Error adding city:', errorData);

                    if (errorData.errors && errorData.errors.plaatsnaam) {
                        this.errorMessage = errorData.errors.plaatsnaam[0];
                    } else if (errorData.errors && errorData.errors.postcode) {
                        this.errorMessage = errorData.errors.postcode[0];
                    } else if (errorData.error) {
                        this.errorMessage = errorData.error;
                    } else {
                        this.errorMessage = 'Er is een fout opgetreden bij het toevoegen van de gemeente';
                    }
                }
            } catch (error) {
                console.error('Error adding city:', error);
                this.errorMessage = 'Er is een fout opgetreden bij het toevoegen van de gemeente';
            }
        }
    }));
});
</script>
