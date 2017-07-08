<?php if(isset($cards)): ?>
	<h3>Searched <?= count($cards) ?> card<?= count($cards) !== 1 ? 's' : null ?> in <span id="time"></span>  second<?= count($cards) !== 1 ? 's' : null ?></h3>
	<h4>If you want an image of all the searched cards <a id="render_canvas" href="javascript:void(0)">click here.</a><br>(Please note that this function is experimental and you won't be able to download the image on mobile devices)</h4>
	<div class="table-responsive">
		<table class="table table-hover">
			<tr>
				<th rowspan="2">Serial</th>
				<th rowspan="2">Name</th>
				<th rowspan="2">Image</th>
				<th rowspan="2">
					Print tag
					<br>
					(pack)
				</th rowspan="2">
				<th colspan="3">Prices</th>
				<th rowspan="2">yugiohprices.com</th>
			</tr>
			<tr>
				<th>Lowest</th>
				<th>Average</th>
				<th>Highest</th>
			</tr>
			<?php foreach($cards as $card): ?>
			<tr>
				<td><?= $card->serial ?></td>
				<td>
					<a target="_blank" href='<?= $card->wiki ?>'>
						<?php if(isset($card->name)): ?>
							<?= $card->name ?>
						<?php else: ?>
							Card not found
						<?php endif ?>
					</a>
				</td>
				<td><a target="_blank" href='<?= $card->image ?>'><img class="card_image" src='<?= $card->image ?>'></a></td>
				<td>
					<?php if(isset($card->print_tag)): ?>
						<?= $card->print_tag ?>
						<br>
						<a target="_blank" href='<?= $card->pack->wiki ?>'>
							<?= $card->pack->name ?>
						</a>
					<?php else: ?>
						No print data
					<?php endif ?>
				</td>
				<?php if(isset($card->prices)): ?>
					<td>$<?= $card->prices->low ?></td>
					<td>$<?= $card->prices->average ?></td>
					<td>$<?= $card->prices->high ?></td>
				<?php else: ?>
					<td colspan="3">No price data</td>
				<?php endif ?>
				<td>
					<a target="_blank" href='<?= $card->yugioh_prices ?>''>
						Check prices on yugiohprices.com
					</a>
				</td>
			</tr>
			<?php endforeach ?>
			<tr>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>-</td>
			<td>$<?= $total->lowest ?></td>
			<td>$<?= $total->average ?></td>
			<td>$<?= $total->highest ?></td>
			<td>-</td>
			</tr>
		</table>
	</div>
<?php endif ?>