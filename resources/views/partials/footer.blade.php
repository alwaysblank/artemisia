<footer class="colophon">
  <div class="container">
    @php dynamic_sidebar('sidebar-footer') @endphp
    <a href="http://attribution.com" <?php if (!is_front_page()) {echo 'rel="nofollow"';} ?> target="_blank">attribution</a>
  </div>
</footer>
