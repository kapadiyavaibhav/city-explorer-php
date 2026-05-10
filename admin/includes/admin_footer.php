<?php
// admin/includes/admin_footer.php
?>
            </div></main></div><footer class="admin-page-footer">
        <div class="admin-container">
            <p>&copy; <?php echo date("Y"); ?> City Explorer Admin Panel. All rights reserved.</p>
        </div>
    </footer>

    <script src="js/admin_scripts.js"></script>
    </body>
</html>
<?php
// Close database connection
if (isset($link) && $link) {
    mysqli_close($link);
}
?>
