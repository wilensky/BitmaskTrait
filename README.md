# BitmaskTrait [![Build Status](https://travis-ci.org/wilensky/BitmaskTrait.png?branch=master)](http://travis-ci.org/wilensky/BitmaskTrait)

PHP7 bitmask modem trait provides seamless integration of bitmasks in your application.

Bitmasks are useful in case when multiple states should be assigned to a single entity or/and exist simultaneously. With help of this trait you can easily pack up to 32/64 states in a single integer like no one else.

## Generic example

Lets take regular invoice to demonstrate the essence.

Assuming invoice has several states:

| _State_ | Sent | Opened | Closed | Failed | Archived |
| ------- | ---- | ------ | ------ | ------ | -------- |
| _Bit_   | 0    | 1      | 2      | 3      | 4        |

And related payment can have more than one state:

| _State_ | In progress | Successful  | Auth. failed | Canceled  | Failed | Refused | Refunded |
| ------- | ----------- | ----------- | ------------ | --------- | ------ | ------- | -------- |
| _Bit_   | 0           | 1           | 2            | 3         | 4      | 5       | 6        |

Looks like to organize states for both invoice and payment we would need **12 fields**, but not with the bitmask.

What we can do is easily **pack all** 12 states **in a single** well known **integer**. Assumed state map can be the following:

| _State_ | Sent | Opened | Closed | Failed | Archived |     | In progress | Successful | Auth. failed | Canceled | Failed | Refused | Refunded |
| ------- | ---- | ------ | ------ | ------ | -------- | --- | ----------- | ----------- | ------------ | --------- | ------ | ------- | -------- |
| _Bit_   | 0    | 1      | 2      | 3      | 4        |     | 5           | 6           | 7            | 8         | 9      | 10      | 11       |

It can look the following way over time:

|                           |     | Bitmask |     | Sent | Opened | Closed | Failed | Archived |     | In progress | Successfull | Auth. failed | Cancelled | Failed | Refused | Refunded |
| ------------------------- | --- | ------- | --- | ---- | ------ | ------ | ------ | -------- | --- | ----------- | ----------- | ------------ | --------- | ------ | ------- | -------- |
| _Bit_                     |     |         |     | 0    | 1      | 2      | 3      | 4        |     | 5           | 6           | 7            | 8         | 9      | 10      | 11       |
|                           |     |         |     |      |        |        |        |          |     |             |             |              |           |        |         |          |
| Invoice sent to customer  |     | 3       |     | +    | +      |        |        |          |     |             |             |              |           |        |         |          |
| Invoice payment initiated |     | 35      |     | +    | +      |        |        |          |     | +           |             |              |           |        |         |          |
| Payment successfull       |     | 69      |     | +    |        | +      |        |          |     |             | +           |              |           |        |         |          |
| Payment refunded          |     | 2117    |     | +    | +      | +      |        |          |     |             | +           |              |           |        |         | +        |
| Payment auth. failed      |     | 137     |     | +    |        |        | +      |          |     |             |             | +            |           |        |         |          |

## Basic usage

```php
<?php

use Wilensky\Traits\BitmaskTrait;

class MyClass
{
    use BitmaskTrait; // Injecting trait in your class
}
```

For ease of use `const` with relevant bit positions can be created.

```php
const INVOICE_SENT = 0;
const INVOICE_OPENED = 1;

// ... for the sake of brevity

const PAYMENT_REFUSED = 10;
const PAYMENT_REFUNDED = 11;
```

### Creating a state

```php
// All further code assumed inside a class scope function as trait has no public methods

$state = 0; // Initial/Zero state

$sent = $this->setBit(
    $state, // Passing initial/existing state to assign few more to
    self::INVOICE_SENT, // (bit 0)
    self::INVOICE_OPENED // (bit 1)
); // 3 (bits 0/1)

// Adding `PAYMENT_INPROGRESS` state to already arranged mask with 2 states
$paymentInit = $this->setBit(
    $sent, // Passing existing mask `3` (with 2 states, 0 and 1 bits)
    self::PAYMENT_INPROGRESS // (bit 5)
); // 35 (bits 0/1/5)

// or the same stuff with use of another method (@see BitmaskTrait::manageMaskBit())
$paymentInit = $this->manageMaskBit($sent, self::PAYMENT_INPROGRESS, true);

// Proceeding to some terminal payment state
$noOpenInProgress = $this->unsetBit(
    $paymentInit, // 35
    self::PAYMENT_INPROGRESS, // Removing `PAYMENT_INPROGRESS` state (bit 5)
    self::INVOICE_OPENED // among with `INVOICE_OPENED` (bit 1)
); // 1 (bits 0)

// to add relevant terminal states
$invoiceClosed = $this->setBit(
    $noOpenInProgress, // Passing mask with relevant unset states
    self::PAYMENT_SUCCESSFUL, // (bit 6)
    self::INVOICE_CLOSED // (bit 2)
); // 69 (bits 0/2/6)
```

### Checking a state for a state

```php
$state = 69; // Invoice closed

$isInvoiceSent = $this->hasBit($state, self::INVOICE_SENT); // true
$isPaymentSuccessful = $this->hasBit($state, self::PAYMENT_SUCCESSFUL); // true
$isRefunded = $this->hasBit($state, self::PAYMENT_REFUNDED); // false
```

### Checking segments

We remember that we have invoice states along with payment states in a single mask.
It is useful sometimes to check whether bit passed relates to a particular segment (_if such are being used_) or not exceeds the mask size itself.

```php
$invoiceSegment = [0, 4]; // Segment range as a single variable

// No exception as state is in allowed range
$this->isBitInRange(self::INVOICE_CLOSED, ...$invoiceSegment);

// `BitAddressingException` will be thrown as payment state is not in allowed range
$this->isBitInRange(self::PAYMENT_SUCCESSFUL, ...$invoiceSegment);
```