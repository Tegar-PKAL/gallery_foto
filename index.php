<?php 
include 'config/database.php';
include 'includes/header.php';

$keyword = mysqli_real_escape_string($conn, $_GET['cari'] ?? '');
$filter_me = $_GET['filter'] ?? ''; 

$where_clause = "WHERE (foto.JudulFoto LIKE '%$keyword%' OR foto.DeskripsiFoto LIKE '%$keyword%')";

if($filter_me == 'me' && isset($_SESSION['UserID'])) {
    $u_id = $_SESSION['UserID'];
    $where_clause .= " AND foto.UserID = '$u_id'";
}

$query = mysqli_query($conn, "SELECT foto.*, user.Username, user.FotoProfil, user.UserID as CreatorID FROM foto 
    JOIN user ON foto.UserID = user.UserID 
    $where_clause
    ORDER BY foto.FotoID DESC");
?>

<style>
    :root {
        --taupe: #463F3A;
        --grey-olive: #8A817C;
        --silver: #BCB8B1;
        --parchment: #F4F3EE;
        --almond-silk: #EBCBC1;
        --bold-accent: #FF5722; 
        --amber-flame: #FFB700; 
    }

    body {
        background-color: var(--parchment);
        color: var(--taupe);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .masonry-grid {
        column-count: 1;
        column-gap: 1.5rem;
        padding: 0 1rem;
        max-width: 1400px;
        margin: 0 auto;
    }

    @media (min-width: 640px) { .masonry-grid { column-count: 2; } }
    @media (min-width: 768px) { .masonry-grid { column-count: 3; } }
    @media (min-width: 1024px) { .masonry-grid { column-count: 4; } }

    .masonry-item {
        break-inside: avoid;
        margin-bottom: 1.5rem;
        background-color: var(--parchment); 
        border: 1px solid var(--silver);
        border-radius: 16px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 4px 10px rgba(70, 63, 58, 0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
    }

    .masonry-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 25px rgba(70, 63, 58, 0.15);
        border-color: var(--almond-silk);
    }

    .masonry-img {
        width: 100%;
        display: block;
        object-fit: cover;
    }

    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: var(--parchment); }
    ::-webkit-scrollbar-thumb { background: var(--silver); border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--grey-olive); }
</style>

<div class="w-full py-8 md:py-12">
    <header class="text-center mb-12 flex flex-col items-center justify-center">
        
        <h1 class="text-3xl md:text-5xl font-black mb-2 tracking-tight" style="color: var(--amber-flame); text-shadow: 1px 1px 2px rgba(0,0,0,0.1);">
            <?= ($filter_me == 'me') ? 'KARYAKU' : 'LANDSPIC' ?>
        </h1>
        <p class="text-xs md:text-sm tracking-[0.2em] uppercase font-semibold" style="color: var(--grey-olive);">
            <?php if($filter_me == 'me'): ?>
                Melihat arsip karyamu
            <?php else: ?>
                Landmark<span class="mx-2" style="color: var(--silver);">|</span> Dunia
            <?php endif; ?>
        </p>

        <?php if(!empty($keyword)): ?>
            <div class="mt-6 px-6 py-2 bg-white/50 border border-[var(--silver)] rounded-full backdrop-blur-sm inline-block shadow-sm">
                <p class="text-sm font-bold" style="color: var(--taupe);">
                    Menampilkan hasil pencarian: <span style="color: var(--amber-flame);"><?= htmlspecialchars($keyword) ?></span>
                </p>
            </div>
        <?php endif; ?>

    </header>

    <div class="masonry-grid">
        <?php if(mysqli_num_rows($query) == 0): ?>
            <div class="col-span-full text-center py-20 w-full">
                <span class="text-6xl mb-4 block">🔍</span>
                <h3 class="text-xl font-bold" style="color: var(--taupe);">Tidak ada foto yang ditemukan</h3>
                <p class="text-sm mt-2" style="color: var(--grey-olive);">Coba cari dengan kata kunci atau hashtag lain.</p>
                <a href="index.php" class="inline-block mt-6 px-6 py-2 bg-[var(--amber-flame)] text-[var(--taupe)] font-bold rounded-lg hover:opacity-80 transition">Kembali ke Beranda</a>
            </div>
        <?php else: ?>
            <?php while($data = mysqli_fetch_array($query)): 
                $fID = $data['FotoID'];
                $creatorID = $data['CreatorID'];
                
                $totalLikeCount = mysqli_query($conn, "SELECT COUNT(*) as total FROM likefoto WHERE FotoID = '$fID'");
                $totalLike = mysqli_fetch_assoc($totalLikeCount)['total'];
                
                $totalKomenCount = mysqli_query($conn, "SELECT COUNT(*) as total FROM komentarfoto WHERE FotoID = '$fID'");
                $totalKomen = mysqli_fetch_assoc($totalKomenCount)['total'];
                
                $isLiked = false;
                if(isset($_SESSION['UserID'])){
                    $current_u_id = $_SESSION['UserID'];
                    $checkLike = mysqli_query($conn, "SELECT * FROM likefoto WHERE FotoID = '$fID' AND UserID = '$current_u_id'");
                    if(mysqli_num_rows($checkLike) > 0) $isLiked = true;
                }
            ?>
            <div class="masonry-item group">
                
                <img src="assets/uploads/<?= $data['LokasiFile']; ?>" class="masonry-img" alt="<?= htmlspecialchars($data['JudulFoto']); ?>">
                
                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-all duration-300 flex flex-col justify-between p-5 backdrop-blur-[2px]">
                    
                    <p class="text-xl font-bold leading-tight" style="color: var(--amber-flame);"><?= htmlspecialchars($data['JudulFoto']); ?></p>
                    
                    <div class="mt-auto">
                        <a href="user_space.php?UserID=<?= $creatorID ?>" class="flex items-center gap-2 mb-5 hover:opacity-75 transition">
                            <div class="w-8 h-8 rounded-full overflow-hidden border-2" style="border-color: var(--amber-flame);">
                                <?php if (!empty($data['FotoProfil'])): ?>
                                    <img src="assets/profiles/<?= $data['FotoProfil'] ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($data['Username']) ?>&background=EBCBC1&color=463F3A" class="w-full h-full object-cover">
                                <?php endif; ?>
                            </div>
                            <span class="text-xs font-bold tracking-wider uppercase" style="color: var(--parchment);">@<?= htmlspecialchars($data['Username']); ?></span>
                        </a>
                        
                        <div class="flex items-center justify-between border-t pt-3" style="border-color: rgba(255, 255, 255, 0.2);">
                            
                            <div class="flex space-x-4">
                                <button onclick="toggleLike(<?= $fID ?>)" class="flex items-center gap-1 focus:outline-none transition-transform hover:scale-110">
                                    <span id="heart-<?= $fID ?>" class="text-xl <?= $isLiked ? 'text-[#FF5722]' : 'text-[#F4F3EE]' ?>">
                                        <?= $isLiked ? '♥' : '♡' ?>
                                    </span>
                                    <span id="like-count-<?= $fID ?>" class="text-xs font-bold" style="color: var(--parchment);"><?= $totalLike; ?></span>
                                </button>
                                
                                <div class="flex items-center gap-1">
                                    <span class="text-lg" style="color: var(--parchment);">💬</span>
                                    <span class="text-xs font-bold" style="color: var(--parchment);"><?= $totalKomen; ?></span>
                                </div>
                            </div>
                            
                            <a href="detail.php?id=<?= $data['FotoID']; ?>" 
                               class="px-5 py-2 rounded-lg text-xs font-bold tracking-widest uppercase transition-all shadow-sm hover:shadow-md"
                               style="background-color: var(--amber-flame); color: #000;">
                                Lihat
                            </a>
                        </div>
                    </div>
                </div>
                
            </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleLike(fotoID) {
    fetch('config/proses_like_ajax.php?id=' + fotoID)
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            document.getElementById('like-count-' + fotoID).innerText = data.total_like;
            const heart = document.getElementById('heart-' + fotoID);
            
            if(data.action === 'like') {
                heart.classList.remove('text-[#F4F3EE]');
                heart.classList.add('text-[#FF5722]');
                heart.innerHTML = '♥'; 
            } else {
                heart.classList.remove('text-[#FF5722]');
                heart.classList.add('text-[#F4F3EE]');
                heart.innerHTML = '♡'; 
            }
        } else if(data.status === 'error' && data.message === 'login_required') {
            alert('Silakan login terlebih dahulu untuk menyukai foto ini.');
            window.location.href = 'login.php';
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>

<?php include 'includes/footer.php'; ?>