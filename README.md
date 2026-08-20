# ViewMend Core for TYPO3

`viewmend/typo3-core` is the small, product-neutral shell shared by official
ViewMend TYPO3 extensions. It owns the top-level **ViewMend** backend entry, its
Dashboard catalogue, and the contract used by installed products to describe
themselves.

It does not provide Site Tracker, InboxMend, Auto-replies, cloud transport, or
form handling. Products remain independent Composer packages and repositories.

## Install

Normally this package is installed as a dependency of a ViewMend product. It
can also be required directly:

```bash
composer require viewmend/typo3-core
vendor/bin/typo3 extension:setup
```

In Composer mode the Dashboard never executes Composer from a backend
request. It shows the exact command and offers a copy action. Classic-mode
installation stays delegated to TYPO3's Extension Manager.

## Product contract

Every product is a separate Composer package and Git repository. It requires
`viewmend/typo3-core`, registers its own direct child module below `viewmend`,
and exposes one tagged `ProductProviderInterface` service. Products must never
require another ViewMend product.
