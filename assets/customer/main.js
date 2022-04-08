var customer = {
  apiKey: null,
  is_submited: false, //will be loaded within the invoice resource
  payButton: "#pay-button",
  wConnectBtn: "#w-connect-div",
  mConnectBtn: "#m-connect-div",
  payWcBtn: "#pay-wc-btn",
  payForm: "#payment-form",
  invoice: JSON.parse(invoice),
  invoice_total: null,
  base_api: APP_BASE_URL + "customer/apiv1/",
  walletAddress: $("#hidden_wallet_address").val(),
  region: null,
  payment_type: null,
  currentInvoiceObj: null, //it is populated when the ajax call is performed, we keep it global for general use,
  formatter: null, //https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Intl/NumberFormat
  options: {
    environment: null, //TEST / LIVE //will be loaded within the invoice resource
    style: {
      input: {
        "font-size": "13px",
        color: "#1A1A1A!important",
        "font-family": "Open Sans, sans-serif;",
        "font-weight": "300",
      },
      "::placeholder": {
        color: "#bbbbc2!important",
        "font-family": "Open Sans, sans-serif;",
        "font-weight": "300",
      },
    },
    fields: {
      cardNumber: {
        selector: "#cardNumber",
        placeholder: "Card Number",
      },
      expiryDate: {
        selector: "#cardExpiry",
        placeholder: "MM / YY",
      },
      cvv: {
        selector: "#cardCvc",
        placeholder: "CVV",
      },
    },
  },
  _get_payments_tab(methods) {
    methods = JSON.parse(methods);
    tab = {};
    if (methods.includes("CC") && methods.includes("BANK")) {
      tab.tabs = `<div role=\"tablist\" aria-orientation=\"horizontal\" aria-label=\"Métodos de pago\" class=\"Tabs-TabList\"><div role=\"presentation\" class=\"Tabs-TabListItemContainer\"><button class=\"Tabs-TabListItem\"    id=\"card-tab\"  type=\"button\" tabindex=\"0\"><div class=\"Tabs-TabListItemContent\"><div class=\"Tabs-TabListPaymentMethod text-center\">
            <div><i class="ni ni-credit-card fa-2x theme_foreground_color"></i></div>  
            <div class=\"Tabs-TabListPaymentLabel\">Card</div> </div></div></button></div> <div role=\"presentation\" class=\"Tabs-TabListItemContainer\"> <button class=\"Tabs-TabListItem\" id=\"ach-tab\"  role=\"tab\" type=\"button\"   >  <div class=\"Tabs-TabListItemContent\"><div class=\"Tabs-TabListPaymentMethod text-center\"><div>
            <i class="fa fa-bank fa-2x theme_foreground_color"></i>&nbsp;</div><div class=\"Tabs-TabListPaymentLabel\">Bank Transfer</div></div></div></button></div><div role=\"presentation\" class=\"Tabs-TabListItemContainer\"> 
            <button class=\"Tabs-TabListItem\" id=\"bit-coin-tab\"  role=\"tab\" type=\"button\"   >  
                <div class=\"Tabs-TabListItemContent\">
                    <div class=\"Tabs-TabListPaymentMethod text-center\">
                        <div><i class="fab fa-ethereum fa-2x theme_foreground_color"></i>&nbsp;</div>
                        <div class=\"Tabs-TabListPaymentLabel\">Crypto</div>
                    </div>
                </div>
            </button>
        </div></div>`;
    }
    if (methods.includes("CC")) {
      tab.cc = customer._get_cc_form();
    }
    if (methods.includes("BANK")) {
      tab.bank = customer._get_bank_form();
    }
    return tab;
  },
  _get_cc_form() {
    return `<div class=\"PaymentForm-paymentMethodForm flex-container spacing-16 direction-column wrap-wrap\"><div class=\"flex-item width-12\"><div class=\"FormFieldGroup\"><div class=\"FormFieldGroup-labelContainer flex-container justify-content-space-between\"><label for=\"cardNumber-fieldset\"><span class=\"Text customer_name\">Card information</span></label></div><fieldset class=\"FormFieldGroup-Fieldset\" id=\"cardNumber-fieldset\"><div class=\"FormFieldGroup-container\" id=\"cardNumber-fieldset\"><div class=\"FormFieldGroup-child FormFieldGroup-child--width-12 FormFieldGroup-childLeft FormFieldGroup-childRight FormFieldGroup-childTop\"><div class=\"FormFieldInput\"><div class=\"CheckoutInputContainer\"><span class=\"InputContainer\" data-max=\"\"><div class=\"CheckoutInput CheckoutInput--tabularnums Input\" id=\"cardNumber\"/></span></div><div class=\"FormFieldInput-Icons\"><span class=\"input-group-addon\"><i class=\"fa fa-credit-card theme_foreground_color cc_can_change\" id=\"cc_icon\"/></span></div></div></div><div class=\"FormFieldGroup-child FormFieldGroup-child--width-6 FormFieldGroup-childLeft FormFieldGroup-childBottom\"><div class=\"FormFieldInput\"><div class=\"CheckoutInputContainer\"><span class=\"InputContainer\" data-max=\"\"><div class=\"CheckoutInput CheckoutInput--tabularnums Input Input--empty\" autocomplete=\"cc-exp\" autocorrect=\"off\" spellcheck=\"false\" id=\"cardExpiry\" name=\"cardExpiry\" type=\"tel\" aria-label=\"Fecha de vencimiento\" placeholder=\"MM/AA\" aria-invalid=\"false\" value=\"\"/></span></div></div></div><div class=\"FormFieldGroup-child FormFieldGroup-child--width-6 FormFieldGroup-childRight FormFieldGroup-childBottom\"><div class=\"FormFieldInput has-icon\"><div class=\"CheckoutInputContainer\"><span class=\"InputContainer\" data-max=\"\"><div class=\"CheckoutInput CheckoutInput--tabularnums Input Input--empty\" autocomplete=\"cc-csc\" autocorrect=\"off\" spellcheck=\"false\" id=\"cardCvc\" name=\"cardCvc\" type=\"tel\" aria-label=\"CVC\" placeholder=\"CVC\" aria-invalid=\"false\" value=\"\"/></span></div><div class=\"FormFieldInput-Icon is-loaded\"><svg class=\"Icon Icon--md\" focusable=\"false\" viewBox=\"0 0 32 21\"><g fill=\"none\" fill-rule=\"evenodd\"><g class=\"Icon-fill\"><g transform=\"translate(0 2)\"><path d=\"M21.68 0H2c-.92 0-2 1.06-2 2v15c0 .94 1.08 2 2 2h25c.92 0 2-1.06 2-2V9.47a5.98 5.98 0 0 1-3 1.45V11c0 .66-.36 1-1 1H3c-.64 0-1-.34-1-1v-1c0-.66.36-1 1-1h17.53a5.98 5.98 0 0 1 1.15-9z\" opacity=\".2\"/><path d=\"M19.34 3H0v3h19.08a6.04 6.04 0 0 1 .26-3z\" opacity=\".3\"/></g><g transform=\"translate(18)\"><path d=\"M7 14A7 7 0 1 1 7 0a7 7 0 0 1 0 14zM4.22 4.1h-.79l-1.93.98v1l1.53-.8V9.9h1.2V4.1zm2.3.8c.57 0 .97.32.97.78 0 .5-.47.85-1.15.85h-.3v.85h.36c.72 0 1.21.36 1.21.88 0 .5-.48.84-1.16.84-.5 0-1-.16-1.52-.47v1c.56.24 1.12.37 1.67.37 1.31 0 2.21-.67 2.21-1.64 0-.68-.42-1.23-1.12-1.45.6-.2.99-.73.99-1.33C8.68 4.64 7.85 4 6.65 4a4 4 0 0 0-1.57.34v.98c.48-.27.97-.42 1.44-.42zm4.32 2.18c.73 0 1.24.43 1.24.99 0 .59-.51 1-1.24 1-.44 0-.9-.14-1.37-.43v1.03c.49.22.99.33 1.48.33.26 0 .5-.04.73-.1.52-.85.82-1.83.82-2.88l-.02-.42a2.3 2.3 0 0 0-1.23-.32c-.18 0-.37.01-.57.04v-1.3h1.44a5.62 5.62 0 0 0-.46-.92H9.64v3.15c.4-.1.8-.17 1.2-.17z\"/></g></g></g></svg></div></div></div><div class=\"FormFieldGroup-child FormFieldGroup-child--width-12 FormFieldGroup-childLeft FormFieldGroup-childBottom\"><div class=\"FormFieldInput\"><div class=\"CheckoutInputContainer\"><span class=\"InputContainer\" data-max=\"\"><input class=\"CheckoutInput CheckoutInput--tabularnums Input\" autocorrect=\"off\" id=\"zip_code_card\" name=\"zip_code_card\" type=\"tel\" placeholder=\"Zip Code\"/></span></div></div></div><div style=\"opacity: 0; height: 0px;\"><span class=\"FieldError Text Text-color--red Text-fontSize--13\"><span aria-hidden=\"true\"/></span></div></div></fieldset></div></div></div>`;
  },
  _get_bank_form() {
    let showAchAccountType =
      customer.currentInvoiceObj.organization.region == "US" ? true : false;
    return `<select class=\"CheckoutInput CheckoutInput--tabularnums Input Input--empty\" placeholder=\"ACH account number\" id=\"bank_type\" > 
                    <option value=\"eft\">BANK (EFT)</option> 
                    <option value=\"ach\">BANK (ACH)</option> 
                </select>
                <select class=\"CheckoutInput CheckoutInput--tabularnums Input Input--empty\" id=\"ach_account_type\" style="${
                  !showAchAccountType ? "display: none" : ""
                }" >
                    <option value=\"\">Select an account type</option>
                    <option value=\"SAVINGS\">Savings</option>
                    <option value=\"CHECKING\">Checking</option> 
                    <option value=\"LOAN\">Loan</option> 
                </select>
                <input class=\"CheckoutInput CheckoutInput--tabularnums Input Input--empty\" placeholder=\"First Name\" id=\"first_name\"></input> 
                <input class=\"CheckoutInput CheckoutInput--tabularnums Input Input--empty\" placeholder=\"Last Name\" id=\"last_name\"></input> 
                <input class=\"CheckoutInput CheckoutInput--tabularnums Input Input--empty\" placeholder=\"Account Number\" id=\"account_number\"></input> 
                <input class=\"CheckoutInput CheckoutInput--tabularnums Input Input--empty\" placeholder=\"Transit Number\" id=\"transit_number\"></input> 
                <input class=\"CheckoutInput CheckoutInput--tabularnums Input Input--empty\" placeholder=\"Routing Number\" id=\"routing_number\"></input> 
                <input class=\"CheckoutInput CheckoutInput--tabularnums Input Input--empty\" placeholder=\"Institution ID\" id=\"institution_id\"></input> 
                <input class=\"CheckoutInput CheckoutInput--tabularnums Input Input--empty\" placeholder=\"City\" id=\"city\"></input> 
                <input class=\"CheckoutInput CheckoutInput--tabularnums Input Input--empty\" placeholder=\"Street\" id=\"street\"></input> 
                <input class=\"CheckoutInput CheckoutInput--tabularnums Input Input--empty\" placeholder=\"Postal Code\" id=\"postal_code\"></input>`;
  },
  load_invoice: async function () {
    try {
      var invoice = await fetch(
        customer.base_api + "invoice/" + customer.invoice.hash
      );
      var data = await invoice.json();

      if (data.response.invoice == null) {
        $("#panel").hide();
        $("#general_error_msg").text("Invoice not found");
        $("#general_error").fadeIn();
        return false;
      }

      if (data.response.invoice.status === "C") {
        //verifyx use constants here
        $("#panel").hide();
        $("#general_error_msg").text("Invoice canceled");
        $("#general_error").fadeIn();
        return false;
      }

      customer.currentInvoiceObj = data.response.invoice;

      //https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Intl/NumberFormat
      customer.formatter = new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
      });

      customer.options.environment = data.response.payment_processor.env;
      customer.apiKey = data.response.payment_processor.encoded_keys;
      if (data.response.invoice.status === "P") {
        //verifyx use constants here
        $("#form_payment").hide();
        customer.load_paid({
          ...data.response.invoice,
          element: "#form_details",
        });
        return false;
      }

      if (data.response) {
        //const sum = data.response.invoice.products.map((s)=> parseFloat(s.price*s.quantity).toFixed(2)).reduce((a,e)=>parseInt(a)+parseInt(e), 0) ;
        // $("#form_details, #form_payment").css({"display":"block",'margin:':' 0 auto;!important'})

        tabs = customer._get_payments_tab(
          data.response.invoice.payment_options
        );

        if (tabs) {
          $("#payments-options").html(tabs.tabs);
        }
        if (tabs.cc && tabs.bank) {
          $("#ach-tab-panel").hide();
        } else if (!tabs.cc && tabs.bank) {
          $("#ach-tab-panel").show();
        }

        let tpl = customer.currentInvoiceObj.organization.fees_template;
        let total_amount = customer.currentInvoiceObj.total_amount;

        let isFeeCalculated = false;
        if (tabs.cc) {
          $("#card-tab-panel").html(tabs.cc);
          customer.calculate_fee(tpl, "cc", total_amount);
          isFeeCalculated = true;
        }
        if (tabs.bank) {
          $("#ach-tab-panel").html(tabs.bank);
          if (!isFeeCalculated) {
            customer.calculate_fee(tpl, "bank", total_amount);
          }
        }

        $("#invoice_total").html(
          customer.formatter.format(data.response.invoice.total_amount)
        );
        $("#invoice_due_date").html(
          data.response.invoice.due_date
            ? "Due " + moment(data.response.invoice.due_date).format("LL")
            : "&nbsp;"
        );
        $("#Invoice-downloadButton").on("click", function () {
          window.location = data.response.invoice.pdf_url;
        });

        $("#customer_name").html(
          data.response.invoice.customer.first_name +
            " " +
            data.response.invoice.customer.last_name
        );
        $(".customer_memo").html(data.response.invoice.memo);
        $("#orgSub_name").html(
          data.response.invoice.suborganization
            ? data.response.invoice.organization.name +
                "<br>" +
                data.response.invoice.suborganization.name
            : data.response.invoice.organization.name
        );
        $("#invoice_").html(data.response.invoice.reference);
        if (data.response.invoice.products.length !== 0) {
          data.response.invoice.products.forEach((element) => {
            $("#detail").after(`
                <tr>
                    <td  style=" border: 0;border-collapse: collapse; margin: 0;padding: 0px 0px 10px 0px;width: 100%; ">
                        <span class="product-name">
                        ${element.product_inv_name}
                        </span><br>
                        <span class="product-qty">
                        Qty ${element.quantity}
                        </span>
                    </td>
                    <td style=" border: 0; border-collapse: collapse; margin: 0; padding: 0; text-align: right; vertical-align: top; ">
                        <span class="span-amount">
                            ${customer.formatter.format(
                              element.product_inv_price
                            )} 
                        </span>
                    </td>
                    
                </tr>
            `);
          });
        }

        $("#total_invoice").html(
          customer.formatter.format(data.response.invoice?.total_amount)
        );
        customer.total_amount = customer.formatter.format(
          data.response.invoice?.total_amount
        );
        $(customer.payButton).html(
          "Pay " +
            customer.formatter.format(data.response.invoice?.total_amount)
        );
        customer.region = data.response.invoice.organization.region; //='EU'
        if (data.response.invoice.organization.region === "CA") {
          $('#bank_type option[value="eft"]').attr("selected", true);
          $("#bank_type").attr("disabled", "disabled");
          $("#routing_number").css("display", "none");
          $("#institution_id").css("display", "block");
          $("#transit_number").css("display", "block");
        } else {
          $('#bank_type option[value="ach"]').attr("selected", true);
          $("#bank_type").attr("disabled", "disabled");
          $("#institution_id").css("display", "none");
          $("#transit_number").css("display", "none");
          $("#routing_number").css("display", "block");
        }
      }
    } catch (e) {
      loader("hide");
      throw e;
    }
  },
  load_paid(obj = {}) {
    $(obj.element).html(`
        <div class="App-contents flex-container spacing-16 direction-column width-12 mt-4 mx-2">
        <div class="flex-item width-auto">
        <div class="row">
        <div class="col-lg-4"></div>
        <div class="col-lg-4">
        <div class="ContentCard">
            <div class="InvoiceSummary pt-3 pb-3">
                <div class="InvoiceSummaryPostPayment flex-container direction-column align-items-center" data-testid="invoice-summary-post-payment">
                    <div class="text-center mb-2">
                        <img src="${
                          base_assets + "images/tick.png"
                        }" width=\"100\"  class=\"Icon Icon--md\">
                    </div>
                    <span class="Text-fontSize--14">Invoice Paid</span>
                    <span class="toFrom mt-1 Text-fontSize--12 mt-2 mb-3">
                        <u>
                            <a href="${
                              customer.currentInvoiceObj.payments[0]
                                ._receipt_file_url
                            }"  class="button"> Download Receipt <i class="fas fa-arrow-down"></i></a>                            
                        </u>
                    </span>                    
                    <strong class="InvoiceSummaryPostPaymentAmount font-bold mt-2" data-testid="invoice-amount-post-payment">${customer.formatter.format(
                      obj.total_amount
                    )}</strong>                                        
                    <div   id="collapsibleDetails" style="width:100%">
                        <table cellpadding="0" cellspacing="0" style="width: 100%;" id="product_details">
                        <tbody>
                            <tr>
                                <td height="26" style="border: 0; margin: 0; padding: 0; font-size: 1px; line-height: 1px; max-height: 1px; mso-line-height-rule: exactly;">
                                    <div class="st-Spacer st-Spacer--filler">&nbsp;</div>
                                </td>
                            </tr>
                            ${obj.products.reduce(
                              (updated, latest) =>
                                updated.concat(`<tr>
                                    <td style="border: 0;border-collapse: collapse;margin: 0;padding: 0;min-width: 32px; width: 32px; font-size: 1px;">&nbsp;</td>
                                    <td style=" border: 0;border-collapse: collapse; margin: 0;padding: 0px 0px 10px 0px;width: 100%; "> 
                                        <span class="product-name">
                                            ${latest.product_inv_name}
                                        </span><br>
                                        <span class="product-qty">
                                            Qty ${latest.quantity}
                                        </span>
                                        <span><br>_get_cc_form
                                            ${
                                              latest.digital_content
                                                ? '<a class="product_file"  href="' +
                                                  latest.digital_content_url +
                                                  '">Download Deliverable</a>'
                                                : ""
                                            }
                                        </span>
                                    </td>
                                    <td style=" border: 0; border-collapse: collapse; margin: 0; padding: 0; min-width: 16px; width: 16px; font-size: 1px; "></td>
                                    <td style=" border: 0; border-collapse: collapse; margin: 0; padding: 0; text-align: right; vertical-align: top; "> 
                                        <span style=" font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Ubuntu, sans-serif; text-decoration: none; color: #1A1A1A; font-size: 14px; line-height: 16px; font-weight: 500; white-space: nowrap; ">
                                            ${customer.formatter.format(
                                              latest.product_inv_price
                                            )}
                                        </span> 
                                    </td>
                                    <td style="border: 0;border-collapse: collapse;margin: 0;padding: 0;min-width: 32px; width: 32px; font-size: 1px;">&nbsp;</td></tr>
                            `),
                              ""
                            )}
                            <tr>
                                <td height="26" style="border: 0; margin: 0; padding: 0; font-size: 1px; line-height: 1px; max-height: 1px; mso-line-height-rule: exactly;">
                                    <div class="st-Spacer st-Spacer--filler">&nbsp;</div>
                                </td>
                            </tr>
                            <tr>
                                <td style="border: 0;border-collapse: collapse;margin: 0;padding: 0;min-width: 32px; width: 32px; font-size: 1px;">&nbsp; </td>
                                <td colspan="3" height="1" style=" border: 0; border-collapse: collapse; margin: 0; padding: 0; height: 1px; font-size: 1px; background-color: #ebebeb; line-height: 1px;"></td>
                            </tr>
                            <tr>
                                <td colspan="3" height="16" style="border: 0;border-collapse: collapse;margin: 0;padding: 0;height: 16px;font-size: 1px;line-height: 1px;mso-line-height-rule: exactly; ">&nbsp;</td>
                            </tr>
                            <tr>
                                <td style=" border: 0; border-collapse: collapse; margin: 0; padding: 0; min-width: 32px; width: 32px; font-size: 1px; "></td>
                                <td colspan="2" style="border: 0;border-collapse: collapse;margin: 0;padding: 0;width: 100%;"> <span style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Ubuntu, sans-serif;text-decoration: none;color: #1A1A1A;font-size: 14px;line-height: 16px;font-weight: 500; word-break: break-word; ">
                                        Total
                                </span> </td>
                                <td style=" border: 0; border-collapse: collapse; margin: 0; padding: 0; text-align: right; vertical-align: top; "> <span id="total_invoice" style=" font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Ubuntu, sans-serif; text-decoration: none; color: #1A1A1A; font-size: 14px; line-height: 16px; font-weight: 500; white-space: nowrap; ">
                                ${customer.formatter.format(
                                  obj.total_amount
                                )}</span> </td>
                            </tr>
                            <tr>
                                <td height="26" style="border: 0; margin: 0; padding: 0; font-size: 1px; line-height: 1px; max-height: 1px; mso-line-height-rule: exactly;">
                                    <div class="st-Spacer st-Spacer--filler">&nbsp;</div>
                                </td>
                            </tr>
                            <tr>
                                <td style="border: 0;border-collapse: collapse;margin: 0;padding: 0;height: 1px;font-size: 1px;line-height: 1px;mso-line-height-rule: exactly; "></td>
                                
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="App-InvoiceDetails flex-item width-grow flex-container direction-column">
                <table class="InvoiceDetails-table">
                    <tbody>
                    <tr class="LabeledTableRow LabeledTableRow--wide">
                        <td style="vertical-align: top; width: 1px; white-space: nowrap;"><span class="Text-fontSize--14">Invoice Reference</span></td>
                        <td style="vertical-align: top; text-align: right;"><span class="Text Text-color--default Text-fontSize--14">${
                          obj.reference
                        }</span></td>
                    </tr>
                    <tr class="LabeledTableRow LabeledTableRow--wide">
                       <!-- <td style="vertical-align: top; width: 1px; white-space: nowrap;"><span class="Text Text-color--gray400 Text-fontSize--14">Due Date</span></td>
                        <td style="vertical-align: top; text-align: right;"><span class="Text Text-color--default Text-fontSize--14">${moment(
                          obj.paid_date
                        ).format("L")}</span></td> -->
                    </tr>
                    </tbody>
                </table>
            </div>
            </div>
            </div>
         </div>  
         </div>                                    
        </div>
        `);
  },
  paysafe_init: function () {
    paysafe.fields.setup(
      customer.apiKey,
      customer.options,
      function (instance, error) {
        if (error) {
          return false;
        }
        var $form = $(customer.payForm);
        var payButton = $(customer.payButton);
        var wConnectBtn = $(customer.wConnectBtn);
        var mConnectBtn = $(customer.mConnectBtn);
        var payWcBtn = $(customer.payWcBtn);

        instance
          .fields("cvv cardNumber expiryDate")
          .valid(function (eventInstance, event) {
            $(event.target.containerElement)
              .closest(".form-control")
              .removeClass("error")
              .addClass("success");
          });
        instance
          .fields("cvv cardNumber expiryDate")
          .invalid(function (eventInstance, event) {
            $(event.target.containerElement)
              .closest(".form-control")
              .removeClass("success")
              .addClass("error");
          });
        instance.fields.cardNumber.on(
          "FieldValueChange",
          function (instance, event) {
            if (!event.data.isEmpty) {
              //completar
              var cardBrand = event.data.cardBrand.replace(/\s+/g, "");
              cardBrand = null; //disabling it, we need better icons
              switch (cardBrand) {
                case "AmericanExpress":
                  $(this)
                    .parents("form")
                    .find(".cc_can_change")
                    .removeClass("fa-credit-card")
                    .addClass("fa-cc-amex");
                  break;
                case "MasterCard":
                  $(this)
                    .parents("form")
                    .find(".cc_can_change")
                    .removeClass("fa-credit-card")
                    .addClass("fa-cc-mastercard");
                  break;
                case "Visa":
                  $(this)
                    .parents("form")
                    .find(".cc_can_change")
                    .removeClass("fa-credit-card")
                    .addClass("fa-cc-visa");
                  break;
                case "Diners":
                  $(this)
                    .parents("form")
                    .find(".cc_can_change")
                    .removeClass("fa-credit-card")
                    .addClass("fa-cc-diners-club");
                  break;
                case "JCB":
                  $(this)
                    .parents("form")
                    .find(".cc_can_change")
                    .removeClass("fa-credit-card")
                    .addClass("fa-cc-jcb");
                  break;
                case "Maestro":
                  $(this)
                    .parents("form")
                    .find(".cc_can_change")
                    .removeClass("fa-credit-card")
                    .addClass("fa-cc-discover");
                  break;
              }
            } else {
              $(this)
                .parents("form")
                .find(".cc_can_change")
                .removeClass()
                .addClass("fa fa-credit-card");
            }
          }
        );
        payButton.bind("click", async function (event) {
          event.preventDefault();
          if (customer.is_submited) {
            return false;
          }
          customer.is_submited = true;
          if (customer.payment_type === "cc") {
            instance.tokenize(null, async function (instance, error, result) {
              if (error || $("#zip_code_card").val() == "") {
                customer.is_submited = false;
                console.log(error);
                if ($("#zip_code_card").val() == "") {
                  $(".payment-errors").text("Enter a valid ZipCode");
                  $(".payment-errors").closest(".row").show();
                  return;
                }
                $(customer.payButton).html("Try again").prop("disabled", false);
                $(".payment-errors").text(error.displayMessage);
                $(".payment-errors").closest(".row").show();
              } else {
                $(customer.payButton).html(
                  'Processing <i class="fa fa-spinner fa-pulse text-light"></i>'
                );
                $(".payment-errors").closest(".row").hide();
                $(".payment-errors").text("");
                try {
                  const rawResponse = await fetch(
                    customer.base_api + "pay/invoice/" + customer.invoice.hash,
                    {
                      method: "POST",
                      headers: {
                        Accept: "application/json",
                        "Content-Type": "application/json",
                      },
                      body: JSON.stringify({
                        payment_method: "credit_card",
                        data_payment: {
                          postal_code: $("#zip_code_card").val(),
                          single_use_token: result.token,
                        },
                        csrf_token: $("[name='csrf_token']").val(),
                      }),
                    }
                  );
                  const content = await rawResponse.json();
                  if (content.error === 1) {
                    $(".payment-errors").text(
                      content.response.errors.join("\n")
                    );
                    $(".payment-errors").closest(".row").show();
                    customer.is_submited = false;
                    setTimeout(function () {
                      $(customer.payButton).html(
                        "Pay $" + customer.total_amount
                      );
                      $(customer.payButton).prop("disabled", false);
                    }, 2000);
                  } else {
                    $(customer.payButton).html(
                      'Payment successful <i class="fa fa-check"></i>'
                    );
                    $(customer.payButton).prop("disabled", true);
                    $("#form_payment").hide();
                    customer.currentInvoiceObj = content.response.invoice;
                    customer.load_paid({
                      ...customer.currentInvoiceObj,
                      element: "#form_details",
                    });
                  }
                } catch (e) {
                  $(customer.payButton).html("Error... Try it again later...");
                  customer.is_submited = false;
                  setTimeout(function () {
                    $(customer.payButton).html("Pay $" + customer.total_amount);
                  }, 2000);
                  $(customer.payButton).prop("disabled", false);
                  throw e;
                }
              }
            });
          } else if (customer.payment_type === "bank") {
            var data = {
              payment_method: "bank_account",
            };
            if (customer.region === "CA") {
              data.data_payment = {
                bank_type: "eft",
                first_name: $("#first_name").val(),
                last_name: $("#last_name").val(),
                account_number: $("#account_number").val(),
                transit_number: $("#transit_number").val(),
                institution_id: $("#institution_id").val(),
                country: "CA",
                city: $("#city").val(),
                street: $("#street").val(),
                postal_code: $("#postal_code").val(),
              };
            } else {
              data.data_payment = {
                bank_type: "ach",
                account_type: $("#ach_account_type").val(),
                first_name: $("#first_name").val(),
                last_name: $("#last_name").val(),
                account_number: $("#account_number").val(),
                routing_number: $("#routing_number").val(),
                country: "US",
                city: $("#city").val(),
                street: $("#street").val(),
                postal_code: $("#postal_code").val(),
              };
            }
            for (var key in data.data_payment) {
              if (data.data_payment[key] == "") {
                $(".payment-errors").text(
                  "There are missing fields, try again"
                );
                $(".payment-errors").closest(".row").show();
                return;
              }
            }

            $(".payment-errors").closest(".row").hide();
            $(".payment-errors").text("");
            // data.csrf_token = $("[name='csrf_token']").val();
            try {
              $(customer.payButton).html(
                'Processing <i class="fa fa-spinner fa-pulse text-light"></i>'
              );
              const rawResponse = await fetch(
                customer.base_api + "pay/invoice/" + customer.invoice.hash,
                {
                  method: "POST",
                  headers: {
                    Accept: "application/json",
                    "Content-Type": "application/json",
                  },
                  body: JSON.stringify(data),
                }
              );
              const content = await rawResponse.json();
              if (rawResponse.status == 200) {
                // console.log(content);
                if (content.error === 1) {
                  $(".payment-errors").text(content.response.errors.join("\n"));
                  $(".payment-errors").closest(".row").show();

                  $(customer.payButton).html("Pay $" + customer.total_amount);
                  return;
                } else {
                  $(customer.payButton).html(
                    'Payment successful <i class="fa fa-check"></i>'
                  );
                  $(customer.payButton).prop("disabled", true);

                  $("#form_payment").hide();
                  customer.currentInvoiceObj = content.response.invoice;
                  customer.load_paid({
                    ...customer.currentInvoiceObj,
                    element: "#form_details",
                  });
                }
              } else {
                $(".payment-errors").text("Error try it again later...");
                $(customer.payButton).html("Pay $" + customer.total_amount);
                $(".payment-errors").closest(".row").show();
                throw rawResponse.status;
              }
            } catch (e) {
              throw e;
            }
          } else if (customer.payment_type === "crypto") {
            //customer.launchTransak();
            //$(customer.payButton).html('Processing <i class="fa fa-spinner fa-pulse text-light"></i>');
            // await $.post(customer.base_api + 'pay/crypto', {
            //     hash: customer.invoice.hash,
            //     csrf_token: $("[name='csrf_token']").val()
            // });
            var amount = customer.currentInvoiceObj.total_amount;
            $("#pay-button").css("display", "none");
            $("#w-connect-div").css("display", "none");
            $("#crypto-tab-panel").html("");
            $("#crypto-tab-panel").css("display", "block");
            $("#crypto-tab-panel").html(`
            <iframe scrolling="no" id="transak-iframe" height="625" title="Transak"
            src="https://global.transak.com?apiKey=2d5b6ee5-ea36-4897-9a95-40e9d8f8dd4f&defaultCryptoCurrency=ETH&fiatCurrency=USD&fiatAmount=${amount}&walletAddress=${customer.walletAddress}" frameborder="no" allowtransparency="true" allowfullscreen="" style="display: block; width: 100%; max-height: 625px; max-width: 500px;">
            </iframe>`);
          } else if (customer.payment_type === "metamaskConnect") {
            if (
              customer.walletAddress != null &&
              customer.walletAddress != ""
            ) {
              customer.web3Login();
              customer.web3Pay(
                customer.currentInvoiceObj.total_amount,
                customer.walletAddress
              );
            }
          }
        });

        payWcBtn.bind("click", async function () {
          var provider = new WalletConnectProvider.default({
            rpc: {
              1: "https://cloudflare-eth.com/", // https://ethereumnodes.com/
              137: "https://polygon-rpc.com/", // https://docs.polygon.technology/docs/develop/network-details/network/
            },
          });
          await provider.enable();
          //  Create Web3 instance
          const web3 = new Web3(provider);
          // console.log(web3);
          window.w3 = web3;
          // console.log(w3);
          var accounts = await web3.eth.getAccounts(); // get all connected accounts
          var account = accounts[0]; // get the primary account
          if (w3) {
            console.log("w3 found");
            //const toAddress = $("#w-address").val();
            await w3.eth.sendTransaction({
              from: account,
              to: customer.walletAddress,
              value: customer.currentInvoiceObj.total_amount,
            });
          }
          //else {
          //await provider.disconnect();
          //}
        });

        wConnectBtn.bind("click", function () {
          if (customer.payment_type === "crypto") {
            $("#w-address").val(customer.walletAddress);
            $("#w-address-div").css("display", "block");
            customer.payment_type = "walletConnect";
            $("#pay-button").css("display", "none");
            $("#wc-div").css("display", "block");
          }
        });

        mConnectBtn.bind("click", function () {
          if (customer.payment_type === "crypto") {
            $("#w-address").val(customer.walletAddress);
            $("#w-address-div").css("display", "block");
            customer.payment_type = "metamaskConnect";
            $("#pay-button").css("display", "block");
            $("#wc-div").css("display", "none");
          }
        });
      }
    );
  },
  isMobile: function () {
    return window.matchMedia("only screen and (max-width: 760px)").matches;
  },
  web3Login: async function () {
    if (!window.ethereum) {
      alert("MetaMask not detected. Please install MetaMask first.");
      return;
    }
    const accounts = await ethereum
      .request({
        method: "eth_requestAccounts",
      })
      .then((result) => {
        //customer.wConnectAddress = result[0];
      })
      .catch((err) => {});
  },
  web3Pay: function (amount, toAddress) {
    const { ethereum } = window;
    if (ethereum) {
      const provider = new ethers.providers.Web3Provider(ethereum);
      const signer = provider.getSigner();
      // Create a transaction object
      let tx = {
        to: toAddress,
        // Convert currency unit from ether to wei
        value: ethers.utils.parseEther(amount),
      };
      // Send a transaction
      signer.sendTransaction(tx).then((txObj) => {
        console.log("txHash", txObj.hash);
      });
    }
  },
  calculate_fee: function (tpl, method, amount) {
    //right now we are not using this function, let's keep it for the future

    let fee = 0;
    if (method == "cc") {
      fee = parseFloat(amount) * tpl.var_cc + tpl.kte_cc;
    } else if (method == "bank") {
      fee = parseFloat(amount) * tpl.var_bnk + tpl.kte_bnk;
    }

    feeMoney = customer.formatter.format(fee);
    //console.log(tpl, method, amount, fee, feeMoney)
  },
  /* launchTransak: function() {
         var transak = new TransakSDK.default({
            apiKey: 'ba8cf158-d5d7-49d3-99e8-de749d22c6fe',  // Your API Key
            environment: 'STAGING', // STAGING/PRODUCTION
            hostURL: window.location.origin,
            widgetHeight: '625px',
            widgetWidth: '500px',
            defaultCryptoCurrency: 'ETH', // Example 'ETH'
            walletAddress: '0x2dd94DC4b658F08E33272e6563dAb1758c10b1de', // Your customer's wallet address
            themeColor: '', // App theme color
            fiatCurrency: 'USD', // If you want to limit fiat selection eg 'USD'
            fiatAmount: 200,
            hideExchangeScreen: true,
            disableWalletAddressForm: true,
            email: 'sandeepd.test@gmail.com', // Your customer's email address
            redirectURL: '',
            firstName: "sandy",
            lastName: "Nakamoto",
            mobileNumber: "+19692154942",
            dob: "1990-11-26",
            address: {
                "addressLine1": "170 Pine St",
                "addressLine2": "San Francisco",
                "city": "San Francisco",
                "state": "CA",
                "postCode": "94111",
                "countryCode": "US"
            }
        });

        transak.init();

        transak.on(transak.ALL_EVENTS, (data) => {
            console.log(data)
        });
          // This will trigger when the user marks payment is made.
        transak.on(transak.EVENTS.TRANSAK_ORDER_SUCCESSFUL, (orderData) => {
           console.log(orderData);
            //transak.close();
        });
    }, */
  load_events: function () {
    $("#card-tab").addClass("Tabs-TabListItem--is-selected");
    $("#card-tab-panel").css("display", "block");
    let tpl = customer.currentInvoiceObj.organization.fees_template;
    let total_amount = customer.currentInvoiceObj.total_amount;
    customer.payment_type = "cc";
    $("#card-tab").on("click", function () {
      customer.payment_type = "cc";
      customer.calculate_fee(tpl, customer.payment_type, total_amount);
      $(this).addClass("Tabs-TabListItem--is-selected");
      $("#ach-tab").removeClass("Tabs-TabListItem--is-selected");
      $("#eft-tab").removeClass("Tabs-TabListItem--is-selected");
      $("#bit-coin-tab").removeClass("Tabs-TabListItem--is-selected");
      $("#card-tab-panel").css("display", "block");
      $("#ach-tab-panel").css("display", "none");
      $("#eft-tab-panel").css("display", "none");
      $("#w-connect-div").css("display", "none");
      $("#w-address-div").css("display", "none");
      // $('#pay-button').css('display', 'block');
      $("#crypto-tab-panel").css("display", "none");

      $("#w-connect-div").css("display", "none");
      $("#m-connect-div").css("display", "none");
    });
    $("#ach-tab").on("click", function () {
      customer.payment_type = "bank";
      customer.calculate_fee(tpl, customer.payment_type, total_amount);
      $(this).addClass("Tabs-TabListItem--is-selected");
      $("#card-tab").removeClass("Tabs-TabListItem--is-selected");
      $("#eft-tab").removeClass("Tabs-TabListItem--is-selected");
      $("#bit-coin-tab").removeClass("Tabs-TabListItem--is-selected");
      $("#card-tab-panel").css("display", "none");
      $("#ach-tab-panel").css("display", "block");
      $("#eft-tab-panel").css("display", "none");
      // $('#pay-button').css('display', 'block');
      $("#w-connect-div").css("display", "none");
      $("#w-address-div").css("display", "none");
      $("#crypto-tab-panel").css("display", "none");


      $("#w-connect-div").css("display", "none");
      $("#m-connect-div").css("display", "none");
    });
    $("#bit-coin-tab").on("click", function () {
      customer.payment_type = "crypto";
      $(this).addClass("Tabs-TabListItem--is-selected");
      $("#ach-tab").removeClass("Tabs-TabListItem--is-selected");
      $("#card-tab").removeClass("Tabs-TabListItem--is-selected");
      $("#eft-tab").removeClass("Tabs-TabListItem--is-selected");
      $("#card-tab-panel").css("display", "none");
      $("#ach-tab-panel").css("display", "none");
      $("#eft-tab-panel").css("display", "none");
      // $('#pay-button').css('display', 'none');
      $("#w-address-div").css("display", "none");

      if (customer.isMobile()) {
        $("#w-connect-div").css("display", "block");
        $("#m-connect-div").css("display", "none");
      } else {
        $("#m-connect-div").css("display", "block");
        $("#w-connect-div").css("display", "none");
      }
      /* $("#crypto-tab-panel").html("");
      $("#crypto-tab-panel").css("display", "block");
      $("#crypto-tab-panel").html(`
        <iframe scrolling="no" id="transak-iframe" height="625" title="Transak"
        src="https://staging-global.transak.com?apiKey=ba8cf158-d5d7-49d3-99e8-de749d22c6fe&defaultCryptoCurrency=ETH&fiatCurrency=USD&fiatAmount=50&walletAddress=0xfF21f4F75ea2BbEf96bC999fEB5Efec98bB3f6F4" frameborder="no" allowtransparency="true" allowfullscreen="" style="display: block; width: 100%; max-height: 625px; max-width: 500px;">
        </iframe>`); */
    });

    // $("iframe#transak-iframe").on("load", function() {
    //     let head = $("iframe#transak-iframe").contents().find("head");
    //     let css = '<style>body { overflow: hidden !important; } </style>';
    //     $(head).append(css);
    //     console.log(123);
    // });
  },
  paymentFormReady: function () {
    if (
      $("#cardNumber").hasClass("success") &&
      $("#cardExpiry").hasClass("success") &&
      $("#cardCVC").hasClass("success")
    ) {
      return true;
    } else {
      return false;
    }
  },
  loader: null,
  get_branding_data: async function () {
    await $.get(
      customer.base_api +
        "organization/get_brand_settings/" +
        customer.currentInvoiceObj.church_id +
        (customer.currentInvoiceObj.campus_id
          ? "/" + customer.currentInvoiceObj.campus_id
          : ""),
      function (result) {
        if (result.response.data) {
          if (result.response.data.logo) {
            $("#invoice_logo").show();
            $("#invoice_logo").attr(
              "src",
              result.response.data.entire_logo_url
            );
          } else {
            $("#invoice_logo").hide();
          }
          let theme_color = result.response.data.theme_color
            ? result.response.data.theme_color
            : "#000000";
          let text_theme_color = helpers.getTextColor(theme_color);
          let style = `
                    .theme_color{
                        background: ${theme_color} !important;
                    }.theme_foreground_color{
                        color: ${theme_color} !important;
                    }
                    .text_theme_color{
                        color: ${text_theme_color} !important;
                    }
                    .email_background_color{
                        background: ${
                          result.response.data.button_text_color
                            ? result.response.data.button_text_color
                            : "#F8F8F8"
                        } !important;
                    }
                `;
          $("#css_branding").html(style);
        }
      }
    );
  },
};

(function () {
  $(document).ready(async function () {
    await customer.load_invoice();
    customer.load_events();
    customer.paysafe_init();
    //await customer.get_branding_data();
    loader("hide");
  });
})();
