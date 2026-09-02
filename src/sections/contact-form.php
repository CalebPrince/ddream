<?php
declare(strict_types=1);

$areas   = data_set('areas');
$contact = config('contact', []);

$interests = content_lines('interests');
$steps     = content_items('steps');
?>
<section id="enquiry" class="scroll-mt-24 bg-canvas py-16 lg:py-24" aria-labelledby="form-heading">
  <div class="shell grid items-start gap-10 lg:grid-cols-12 lg:gap-12">

    <!-- The form -->
    <div class="lg:col-span-7">
      <p class="eyebrow"><?= e(content('eyebrow')) ?></p>
      <h2 id="form-heading" class="t-h2 mt-4"><?= e(content('heading')) ?></h2>
      <p class="t-lead mt-3 text-muted"><?= e(content('lead')) ?></p>

      <form class="mt-8 rounded-[10px] border border-hairline bg-surface p-6 shadow-panel lg:p-8"
            action="/contact" method="post" novalidate>

        <fieldset class="border-0 p-0">
          <legend class="t-meta font-semibold uppercase tracking-[0.14em] text-gold-600">
            About you
          </legend>

          <div class="mt-5 grid gap-5 sm:grid-cols-2">
            <div>
              <label class="field-label" for="cf-name">Full name <span class="text-signal-600">*</span></label>
              <input class="field" id="cf-name" name="name" type="text" required autocomplete="name">
            </div>
            <div>
              <label class="field-label" for="cf-email">Email address <span class="text-signal-600">*</span></label>
              <input class="field" id="cf-email" name="email" type="email" required autocomplete="email">
            </div>
            <div>
              <label class="field-label" for="cf-phone">Phone or WhatsApp</label>
              <input class="field" id="cf-phone" name="phone" type="tel" autocomplete="tel"
                     placeholder="Include your country code">
            </div>
            <div>
              <label class="field-label" for="cf-country">Where are you based?</label>
              <input class="field" id="cf-country" name="country" type="text" list="corridorList"
                     autocomplete="country-name" placeholder="City or country">
              <datalist id="corridorList">
                <?php foreach (config('corridors', []) as $city): ?>
                  <option value="<?= e($city) ?>"></option>
                <?php endforeach; ?>
              </datalist>
            </div>
          </div>
        </fieldset>

        <fieldset class="mt-8 border-0 p-0">
          <legend class="t-meta font-semibold uppercase tracking-[0.14em] text-gold-600">
            What you need
          </legend>

          <div class="mt-5 grid gap-5 sm:grid-cols-2">
            <div>
              <label class="field-label" for="cf-interest">I am interested in <span class="text-signal-600">*</span></label>
              <select class="field" id="cf-interest" name="interest" required>
                <option value="">Please choose</option>
                <?php foreach ($interests as $interest): ?>
                  <option><?= e($interest) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="field-label" for="cf-location">Preferred location</label>
              <input class="field" id="cf-location" name="location" type="text" list="areaSuggestionsContact"
                     placeholder="e.g. East Legon, Accra">
              <datalist id="areaSuggestionsContact">
                <?php foreach ($areas['suggestions'] as $suggestion): ?>
                  <option value="<?= e($suggestion) ?>"></option>
                <?php endforeach; ?>
              </datalist>
            </div>
            <div>
              <label class="field-label" for="cf-budget">Budget (USD)</label>
              <select class="field" id="cf-budget" name="budget">
                <option value="">Prefer not to say</option>
                <option>Under $50,000</option>
                <option>$50,000 to $100,000</option>
                <option>$100,000 to $250,000</option>
                <option>$250,000 to $500,000</option>
                <option>Over $500,000</option>
                <option>Rental budget, monthly</option>
              </select>
            </div>
            <div>
              <label class="field-label" for="cf-timeline">Timeline</label>
              <select class="field" id="cf-timeline" name="timeline">
                <option value="">Not sure yet</option>
                <option>Ready now</option>
                <option>Within 3 months</option>
                <option>Within 6 months</option>
                <option>Within a year</option>
                <option>Just researching</option>
              </select>
            </div>
          </div>
        </fieldset>

        <fieldset class="mt-8 border-0 p-0">
          <legend class="t-meta font-semibold uppercase tracking-[0.14em] text-gold-600">
            How to reach you
          </legend>

          <div class="mt-5 grid gap-5 sm:grid-cols-2">
            <div>
              <label class="field-label" for="cf-method">Preferred contact method</label>
              <select class="field" id="cf-method" name="method">
                <option>Email</option>
                <option>WhatsApp</option>
                <option>Phone call</option>
                <option>Video call</option>
              </select>
            </div>
            <div>
              <label class="field-label" for="cf-when">Best time to reach you</label>
              <select class="field" id="cf-when" name="when">
                <option value="">Any time</option>
                <option>Morning, my local time</option>
                <option>Afternoon, my local time</option>
                <option>Evening, my local time</option>
                <option>Weekends only</option>
              </select>
            </div>
          </div>

          <div class="mt-5">
            <label class="field-label" for="cf-message">Your message</label>
            <textarea class="field h-auto py-3" id="cf-message" name="message" rows="5"
                      placeholder="Tell us about the property, the plot, or the problem you are trying to solve."></textarea>
          </div>
        </fieldset>

        <label class="mt-6 flex items-start gap-3 text-[0.9375rem] leading-relaxed text-muted">
          <input type="checkbox" name="consent" required
                 class="mt-1 h-4 w-4 shrink-0 rounded-[2px] border-hairline text-navy-700 focus:ring-navy-700">
          <span>
            <?= e(content('consent')) ?>
            <span class="text-signal-600">*</span>
          </span>
        </label>

        <div class="mt-7 flex flex-col gap-3 sm:flex-row sm:items-center">
          <button type="submit" class="btn btn-primary btn-lg flex-1">
            <?= icon('mail', 'h-[18px] w-[18px]') ?><?= e(content('submit_label')) ?>
          </button>
          <a href="tel:<?= e($contact['phone_href']) ?>" class="btn btn-outline btn-lg">
            <?= icon('phone', 'h-4 w-4') ?><?= e(content('call_label')) ?>
          </a>
        </div>

        <p class="mt-4 flex items-start gap-2 t-meta text-muted">
          <?= icon('shield-check', 'h-4 w-4 shrink-0 translate-y-0.5 text-verified') ?>
          <span><?= e(content('privacy_note')) ?></span>
        </p>
      </form>
    </div>

    <!-- What happens next -->
    <aside class="lg:col-span-5 lg:sticky lg:top-28 lg:self-start">
      <div class="card p-6 lg:p-7">
        <h3 class="t-h3"><?= e(content('next_heading')) ?></h3>
        <ol class="mt-6 space-y-6">
          <?php foreach ($steps as $i => $step): ?>
            <li class="flex gap-4">
              <span class="relative flex flex-col items-center">
                <span class="tabular inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-gold-500/50 bg-canvas font-display text-[0.9375rem] font-semibold text-gold-600">
                  <?= $i + 1 ?>
                </span>
                <?php if ($i < count($steps) - 1): ?>
                  <span class="mt-2 w-px flex-1 bg-gradient-to-b from-gold-500/40 to-transparent" aria-hidden="true"></span>
                <?php endif; ?>
              </span>
              <div class="pb-1">
                <h4 class="font-sans text-[1rem] font-semibold text-navy-700"><?= e($step['title']) ?></h4>
                <p class="mt-1 text-[0.9375rem] leading-relaxed text-muted"><?= e($step['body']) ?></p>
              </div>
            </li>
          <?php endforeach; ?>
        </ol>

        <div class="mt-7 rounded-[6px] border border-gold-200 bg-gold-100/60 p-5">
          <p class="flex items-baseline gap-2 font-display text-xl font-semibold leading-none">
            <span class="text-signal-600">No</span><span class="text-navy-700">Client Commission</span>
          </p>
          <p class="mt-3 t-meta text-muted"><?= e(content('panel_note')) ?></p>
        </div>
      </div>
    </aside>
  </div>
</section>
