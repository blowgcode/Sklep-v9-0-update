# Tpay OpenAPI integration (CHBS)

## Wymagania w panelu Tpay
- Wygeneruj klucze OpenAPI (client_id + secret): **Integration → API → Open API keys**.
- Ustaw **Payment Tpay notification secret** jako merchant confirmation/security code używany do weryfikacji webhooka (to nie jest OAuth `client_secret`).
- W sekcji powiadomień ustaw adres endpointu na:
  `https://twoja-domena.pl/?action=payment_tpay`
- Włącz **Allow override notification URL** tylko jeśli chcesz przekazywać `callbacks.notification.url` z API.
- Upewnij się, że endpoint działa po HTTPS (TLS 1.2+), nie robi redirectów i odpowiada 200.

## Testy (sandbox)
### 1) Test pobrania bank-groups
1. Zaloguj się do WP jako administrator.
2. Otwórz w przeglądarce:
   `https://twoja-domena.pl/?chbs_tpay_test=1&booking_form_id=ID`
3. Oczekiwany wynik: JSON z `bank_groups`.

### 2) Test utworzenia transakcji
1. Zaloguj się do WP jako administrator.
2. Otwórz w przeglądarce:
   `https://twoja-domena.pl/?chbs_tpay_test=1&booking_form_id=ID&group_id=GRUPA`
3. Oczekiwany wynik: JSON z `transaction` zawierającym `paymentUrl`.

> **Uwaga:** tryb testowy jest dostępny tylko dla zalogowanych administratorów.

## Test webhooka (Tpay)
1. W panelu Tpay skorzystaj z narzędzia do testowania notyfikacji (OpenAPI webhook test).
2. Ustaw URL powiadomień: `https://twoja-domena.pl/?action=payment_tpay`.
3. Po poprawnej weryfikacji JWS endpoint powinien zwrócić `TRUE`.
4. Sprawdź logi CHBS – wpisy zawierają `tr_id`, `tr_crc`, `tr_status`, oraz `jws_verified`.

## Filtry integracji
- `chbs_tpay_include_notification_url_in_callbacks` (bool, domyślnie `true`) — kontroluje czy wtyczka dołącza `callbacks.notification.url` podczas tworzenia transakcji (`preparePayment()` i diagnostyka).
  - Ustaw `false`, gdy w panelu Tpay (PoS) wyłączone jest nadpisywanie URL i webhook ma być brany wyłącznie z konfiguracji PoS.

## Checklista wdrożeniowa
- [ ] Klucze OpenAPI (client_id + secret) wpisane w ustawieniach formularza rezerwacji.
- [ ] `Payment Tpay notification secret` ustawiony jako kod security/confirmation zgodny z panelem Tpay.
- [ ] Jeśli używasz `callbacks.notification.url`, wskazuje on na właściwy endpoint i opcja override jest włączona w panelu Tpay.
- [ ] Endpoint notyfikacji nie wykonuje redirectów i odpowiada `TRUE`.
