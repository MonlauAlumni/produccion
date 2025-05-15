<template>
  <footer class="bg-gradient-to-r from-[#193CB8] to-[#2748c6] text-white">
    <!-- Contenido principal del footer -->
    <div class="container mx-auto px-4 py-10">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <!-- Logo y descripción -->
        <div class="space-y-4">
          <a href="/" class="flex items-center gap-3">
            <img src="../../../public/images/logoMonlau.jpg" alt="Alumni" class="w-12 h-12 bg-white p-1 rounded-md">
            <span class="text-2xl font-bold">Alumni</span>
          </a>
          <p class="text-white/80 text-sm mt-4 max-w-xs">
            Conectando a los exalumnos de Monlau FP con oportunidades profesionales y recursos para su desarrollo.
          </p>
          <div class="pt-4">
            <h3 class="font-semibold mb-2">Síguenos</h3>
            <div class="flex gap-3">
              <a href="https://x.com/monlau_fp" target="_blank"
                class="bg-white/10 hover:bg-white/20 p-2   rounded-full transition-colors">
                <i class='bx bxl-twitter   text-xl'></i>
              </a>
              <a href="https://www.facebook.com/monlau.fp/" target="_blank"
                class="bg-white/10 hover:bg-white/20 p-2 rounded-full transition-colors">
                <i class='bx bxl-facebook text-xl'></i>
              </a>
              <a href="https://www.youtube.com/c/MonlauFormaciónProfesional" target="_blank"
                class="bg-white/10 hover:bg-white/20 p-2 rounded-full transition-colors">
                <i class='bx bxl-youtube text-xl'></i>
              </a>
              <a href="https://linkedin.com/school/monlau-formacion-profesional/" target="_blank"
                class="bg-white/10 hover:bg-white/20 p-2 rounded-full transition-colors">
                <i class='bx bxl-linkedin text-xl'></i>
              </a>
              <a href="https://instagram.com/monlau_fp" target="_blank"
                class="bg-white/10 hover:bg-white/20 p-2 rounded-full transition-colors">
                <i class='bx bxl-instagram text-xl'></i>
              </a>
            </div>
          </div>
        </div>

        <!-- Nosotros -->
        <div class="space-y-4">
          <h3 class="text-xl font-semibold border-b border-white/20 pb-2">Nosotros</h3>
          <ul class="space-y-2">
            <li>
              <a href="https://www.monlau.com/formacio-professional/contact-us/"
                class="text-white/80 hover:text-white flex items-center gap-2 transition-colors">
                <i class='bx bx-envelope'></i>
                <span>Contacto</span>
              </a>
            </li>
            <li>
              <a href="https://www.monlau.com/formacio-professional/about-us/"
                class="text-white/80 hover:text-white flex items-center gap-2 transition-colors">
                <i class='bx bx-info-circle'></i>
                <span>Sobre Nosotros</span>
              </a>
            </li>
          </ul>
          <div class="pt-4 text-white/80">
            <div class="flex items-center gap-2 mb-1">
              <i class='bx bx-phone'></i>
              <span>933 408 204</span>
            </div>
            <div class="flex items-center gap-2">
              <i class='bx bx-envelope'></i>
              <a href="mailto:info@monlau.com" class="hover:text-white transition-colors">info@monlau.com</a>
            </div>
          </div>
        </div>

        <!-- Áreas de formación -->
        <div class="space-y-4">
          <h3 class="text-xl font-semibold border-b border-white/20 pb-2">Áreas de Formación</h3>
          <ul class="space-y-2">
            <li>
              <a href="https://www.monlau.com/formacio-professional/automotive-industry-area/"
                class="text-white/80 hover:text-white flex items-center gap-2 transition-colors">
                <i class='bx bx-car'></i>
                <span>Automoción</span>
              </a>
            </li>
            <li>
              <a href="https://www.monlau.com/formacio-professional/information-technology-area/"
                class="text-white/80 hover:text-white flex items-center gap-2 transition-colors">
                <i class='bx bx-code-alt'></i>
                <span>Informática</span>
              </a>
            </li>

            <li>
              <a href="https://www.monlau.com/formacio-professional/business-and-marketing-area/"
                class="text-white/80 hover:text-white flex items-center gap-2 transition-colors">
                <i class='bx bx-briefcase'></i>
                <span>Empresa y Marketing</span>
              </a>
            </li>
          </ul>
        </div>

        <!-- Legal -->
        <div class="space-y-4">
          <h3 class="text-xl font-semibold border-b border-white/20 pb-2">Legal</h3>
          <ul class="space-y-2">
            <li>
              <a href="https://www.monlau.com/aviso-legal/"
                class="text-white/80 hover:text-white flex items-center gap-2 transition-colors">
                <i class='bx bx-file'></i>
                <span>Aviso Legal</span>
              </a>
            </li>
            <li>
              <a href="https://www.monlau.com/politica-de-privacidad/"
                class="text-white/80 hover:text-white flex items-center gap-2 transition-colors">
                <i class='bx bx-lock-alt'></i>
                <span>Política de Privacidad</span>
              </a>
            </li>
            <li>
              <a href="https://www.monlau.com/politica-de-calidad/"
                class="text-white/80 hover:text-white flex items-center gap-2 transition-colors">
                <i class='bx bx-check-shield'></i>
                <span>Política de Calidad</span>
              </a>
            </li>
          </ul>


        </div>
      </div>
    </div>

    <!-- Barra de copyright -->
    <div class="border-t border-white/10">
      <div
        class="container mx-auto px-4 py-4 flex flex-col sm:flex-row justify-between items-center text-sm text-white/70">
        <div>© MONLAU GROUP 2021 | © Alumni 2025, All Rights Reserved</div>
        <div class="mt-2 sm:mt-0">
          Desarrollado con <i class='bx bxs-heart text-red-500 inline-block'></i> por el equipo de Alumni |
          <span class="italic">Desarrollado por Lucas Martínez e Iker Hurtado</span>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
// No necesitamos importar boxicons ya que estamos usando las clases bx directamente
</script>