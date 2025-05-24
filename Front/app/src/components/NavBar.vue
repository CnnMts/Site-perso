<script setup>

</script>

<template>
  <div class="container-nav">
    <div class="Logo">
      <a href="/">
        <img id="logo" src="../assets/navIcon/Logo.webp">
      </a>
    </div>
    <div class="nav-left">
      <div class="search-container">
        <input type="text" class="search-input" placeholder="Rechercher..." />
        <button class="search-button">🔍</button>
      </div>
      <div class="nav-icon">
          <a href="/contact">
            <img src="../assets/navIcon/support.png">
          </a>
      </div>
      <div class="nav-icon">
        <img src="../assets/navIcon/hearth.png">
      </div>
      <div class="nav-icon">
        <img src="../assets/navIcon/compte.png">
      </div>
      <div class="nav-icon">
        <img src="../assets/navIcon/shopping-cart.png">
      </div>
    </div>
  </div>
</template>


<style>

.container-nav, .nav-left {
  display: flex;
  align-items: center;
  flex-direction: row;
}

.search-container {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-left: 80px;
}

.search-input, .search-button {
  padding: 10px;
  font-size: 16px;
  transition: all 0.3s ease;
}

.search-input {
  width: 300px;
  border: 2px solid #ccc;
  border-radius: 5px 0 0 5px;
  outline: none;
}

.search-input:focus {
  border-color: #4CAF50;
  box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
}

.search-button {
  border: none;
  background-color: #4CAF50;
  color: white;
  border-radius: 0 5px 5px 0;
  cursor: pointer;
}

.search-button:hover {
  background-color: #45a049;
}

.nav-icon {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 50px;
}

.nav-icon img {
  height: 50px;
  max-height: 100%;
  max-width: 100%;
  object-fit: contain;
  border-radius: 8px;
}

.Logo{
  display: flex;
  justify-content: center;
  align-items: center;
  height: 80px;
}

#logo{
   height: 80px;
  max-height: 100%;
  max-width: 100%;
  object-fit: contain;
  border-radius: 8px;
}
</style>
