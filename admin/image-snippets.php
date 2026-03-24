<style>
  .card img {
    object-fit: cover;
    height: 180px;
  }
  @media (max-width: 600px) {
    .card img {
      height: 90px;
    }
    .row.g-4 {
      gap: 0.5rem !important;
    }
    .card {
      font-size: 0.95em;
    }
  }
</style></head>
<body>
  
  <div class="container py-4">
    <div id="responseMessage" class="alert d-none" role="alert"></div>

<div class="card mb-4 shadow-sm">
  <div class="card-body">
    <form method="post" enctype="multipart/form-data" class="row g-2 align-items-center">
      <div class="col-12 col-md-8">
        <label for="images" class="form-label mb-1 fw-semibold">Add Images</label>
        <input type="file" id="images" name="images[]" multiple accept="image/*" class="form-control" required>
        <div class="form-text">You can select multiple images (jpg, png, gif).</div>
      </div>
      <div class="col-12 col-md-4 d-flex align-items-end">
        <button type="submit" name="upload" class="btn btn-primary w-100 mt-2 mt-md-0">
          <i class="bi bi-upload"></i> Upload Images
        </button>
      </div>
    </form>
  </div>
</div>

<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
  <a href="manage_stock.php" class="btn btn-secondary mb-2 mb-md-0">← Back to Stock</a>
  <h2 class="mb-0 flex-grow-1 text-center text-md-start">Images for Vehicle ID <?= $stockID ?></h2>
</div>
<div class="alert alert-info mb-4">
  <strong>Tip:</strong> Drag and drop images to change their order. The first image will be used as the main thumbnail. Click <b>Save Order</b> to apply your changes.
</div>

<div class="row g-4" id="imageGrid">
      <?php 
      $orderFile = $imageDir . 'order.json';
if (file_exists($orderFile)) {
  $ordered = json_decode(file_get_contents($orderFile), true);
  // Only include images that still exist
  $images = array_values(array_filter($ordered, function($img) use ($imageDir) {
    return is_file($imageDir . $img);
  }));
}
foreach ($images as $image):
        $filenameNoExt = pathinfo($image, PATHINFO_FILENAME); ?>
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card h-100 shadow-sm">
            <a href="<?= $webImagePath . $image ?>" data-lightbox="car-<?= $stockID ?>">
              <img src="<?= $webImagePath . $image ?>" class="card-img-top" alt="Vehicle image">
            </a>
            <div class="card-body">
              <form class="delete-form" method="POST" action="scripts/delete_image.php">
                <input type="hidden" name="stockID" value="<?= $stockID ?>">
                <input type="hidden" name="filename" value="<?= $image ?>">
                <button class="btn btn-outline-danger btn-sm w-100">Delete</button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <button id="saveOrderBtn" class="btn btn-success mt-3">Save Order</button>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
<script>
function showMessage(type, msg) {
  const $box = $('#responseMessage');
  $box
    .removeClass('d-none alert-success alert-danger')
    .addClass(type === 'success' ? 'alert-success' : 'alert-danger')
    .text(msg)
    .fadeIn();

  setTimeout(() => $box.fadeOut(), 5000);
}


$(document).on('submit', '.delete-form', function(e) {
  e.preventDefault();
  if (!confirm('Delete this image?')) return;

  const $form = $(this);
  const $card = $form.closest('.col-sm-6, .col-md-4');

  $.post($form.attr('action'), $form.serialize(), function(response) {
    if (response.status === 'success') {
      showMessage('success', response.msg);
      $card.fadeOut(300, function() { $(this).remove(); });
    } else {
      showMessage('error', response.msg);
    }
  }, 'json').fail(() => {
    showMessage('error', 'Delete request failed.');
  });
});
</script>
<script>
  // Make the grid sortable
  const sortable = new Sortable(document.getElementById('imageGrid'), {
    animation: 150,
    handle: '.card-img-top', // drag by image
    draggable: '.col-sm-6, .col-md-4, .col-lg-3'
  });

// Save order
document.getElementById('saveOrderBtn').addEventListener('click', function() {
  const order = [];
  document.querySelectorAll('#imageGrid .card').forEach(card => {
    const input = card.querySelector('input[name="filename"]');
    if (input) order.push(input.value);
  });
  fetch('scripts/save_image_order.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({stockID: <?= $stockID ?>, order: order})
  })
  .then(res => res.json())
  .then(data => alert(data.msg || 'Order saved!'));
});</script>
</body>
</html>
