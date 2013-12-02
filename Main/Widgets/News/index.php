<ul class="latest-news">
<?php foreach ($items as $item) {?>
    <li class="clearfix">
        <img class="thumb" src="<?php echo $item['pic']?>" alt="">
        <div class="desc">
            <h3><a class="title" href="/news/<?php echo $item['id']?>"><?php echo $item['title']?></a></h3>
            <div><?php echo $item['desc']?></div>
            <i class="time"><?php echo $item['date']?></i>
        </div>
    </li>
<?php }?>
</ul>