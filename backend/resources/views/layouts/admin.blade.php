
    <!-- Header -->
    @include('partials.header')
    
    <!-- Sidebar -->
    @include('partials.sidebar')
    
    <!-- Main Content Wrapper -->
    <div class="flex flex-col lg:flex-row grow pt-[--header-height] lg:pt-0">
      <!-- Main Content Area -->
      <div class="flex flex-col grow items-stretch rounded-xl bg-background border border-input lg:ms-[--sidebar-width] mt-0 lg:mt-[15px] mr-[15px] mb-[15px] ml-0 lg:ml-[15px]">
        <div class="flex flex-col grow kt-scrollable-y-auto [--kt-scrollbar-width:auto] pt-5" id="scrollable_content">
          <main class="grow" role="content">
            <!-- Content Container - Removed extra container wrapper -->
            <div class="w-full px-5 lg:px-7.5">
              @yield('content')
            </div>
          </main>
          
        @include('partials.footer')
        </div>
      </div>
    </div>
  </div>

  <!-- Search Modal -->
  <div class="kt-modal" data-kt-modal="true" id="search_modal">
    <div class="kt-modal-content max-w-[600px] top-[15%]">
      <div class="kt-modal-header py-4 px-5">
        <i class="ki-filled ki-magnifier text-muted-foreground text-xl"></i>
        <input class="kt-input kt-input-ghost" name="query" placeholder="Tap to start search" type="text" value=""/>
        <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-dim shrink-0" data-kt-modal-dismiss="true">
          <i class="ki-filled ki-cross"></i>
        </button>
      </div>
      <div class="kt-modal-body p-0 pb-5"></div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="assets/js/core.bundle.js"></script>
  <script src="assets/vendors/ktui/ktui.min.js"></script>
  <script src="assets/vendors/apexcharts/apexcharts.min.js"></script>
  <script src="assets/js/widgets/general.js"></script>

  <!-- Theme Toggle Script -->
  <script>
  document.addEventListener('DOMContentLoaded', function() {
      const themeToggle = document.getElementById('theme-toggle');

      if (themeToggle) {
          // Set initial state based on current theme
          const currentTheme = localStorage.getItem('theme') || 'light';
          themeToggle.checked = currentTheme === 'dark';

          // Handle theme toggle
          themeToggle.addEventListener('change', function() {
              const newTheme = this.checked ? 'dark' : 'light';
              localStorage.setItem('theme', newTheme);

              if (newTheme === 'dark') {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
          });
      }
  });
  </script>

  @stack('scripts')
</body>
</html>