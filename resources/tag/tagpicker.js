/*
 * Tagpicker
 * Version 1.0.0
 * Written by: Fabio Miranda
 *
 * @property String $inputId is the ID of the input HTML Element
 * @property Int $maxTags the maximum of tags in this dropdown
 * @property String $tagSearchUrl the url of the search, to find the tags
 * @property String $currentValue is the current value of the parent field.
 *
 */

var tagCount = 0;

$.fn.tagpicker = function (options) {

    // set standard options
    options = $.extend({
        tagSearchUrl: "",
        inputId: "",
        maxTags: 0,
        currentValue: "",
        renderType: "normal", // possible values are "normal", "partial"
        focus: false,
        tagId: "",
        data: {},
        placeholderText: 'Add a tag'
    }, options);

    var chosen = "";
    var uniqueID = "";


    init();


    function init() {

        uniqueID = options.inputId.substr(1);

        var _template = '<div class="' + uniqueID + '_tag_picker_container"><ul class="tag_input" id="' + uniqueID + '_invite_tags"><li id="' + uniqueID + '_tag_input"><input type="text" id="' + uniqueID + '_tag_input_field" class="tag_input_field" value="" autocomplete="off"></li></ul><ul class="dropdown-menu" id="' + uniqueID + '_tagpicker" role="menu" aria-labelledby="dropdownMenu"></ul></div>';

        // remove picker if existing
        $('.'+uniqueID+'_tag_picker_container').remove();


        if ($('.' + uniqueID + '_tag_picker_container').length === 0) {

            // insert the new input structure after the original input element
            $(options.inputId).after(_template);
        }


        // hide original input element
        $(options.inputId).hide();

        if (options.currentValue !== "") {

            // restore data from database
            restoreTags(options.currentValue);
        }

        // add placeholder text to input field
        $('#' + uniqueID + '_tag_input_field').attr('placeholder', options.placeholderText);

        if (options.focus === true) {
            // set focus to input
            $('#' + uniqueID + '_tag_input_field').focus();
            $('#' + uniqueID + '_invite_tags').addClass('focus');
        }

        // simulate focus in
        $('#' + uniqueID + '_tag_input_field').focusin(function () {
            $('#' + uniqueID + '_invite_tags').addClass('focus');
        });

        // simulate focus out
        $('#' + uniqueID + '_tag_input_field').focusout(function () {
            $('#' + uniqueID + '_invite_tags').removeClass('focus');
        });

    }

    function restoreTags(html) {

        // add html structure for input element
        $('#' + uniqueID + '_invite_tags .tagInput').remove();
        $('#' + uniqueID + '_invite_tags').prepend(html);

        // create function for every tag tag to remove the element
        $('#' + uniqueID + '_invite_tags .tagInput i').each(function () {

            $(this).click(function () {

                // remove tag tag
                $(this).parent().remove();

                // reduce the count of added tag
                tagCount--;

            });

            // raise the count of added tag
            tagCount++;

        });


    }


    // Set focus on the input field, by clicking the <ul> construct
    jQuery('#' + uniqueID + '_invite_tags').click(function () {

        // set focus
        $('#' + uniqueID + '_tag_input_field').focus();
    });

    $('#' + uniqueID + '_tag_input_field').keydown(function (event) {

        // by pressing the tab key an the input is empty
        if ($(this).val() === "" && event.keyCode === 9) {

            //do nothing

            // by pressing enter, tab, up or down arrow
        } else if (event.keyCode === 40 || event.keyCode === 38 || event.keyCode === 13 || event.keyCode === 9) {

            // ... disable the default behavior to hold the cursor at the end of the string
            event.preventDefault();

        }

        // if there is a tag limit and the tag didn't press the tab key
        if (options.maxTags !== 0 && event.keyCode !== 9) {

            // if the max tag count is reached
            if (tagCount === options.maxTags) {

                // show hint
                showHintTag();

                // block input events
                event.preventDefault();
            }
        }

    });

    $('#' + uniqueID + '_tag_input_field').keyup(function (event) {

        // start search after a specific count of characters
        if ($('#' + uniqueID + '_tag_input_field').val().length >= 2) {

            // set tagpicker position in bottom of the tag input
            $('#' + uniqueID + '_tagpicker').css({
                position: "absolute",
                top: $('#' + uniqueID + '_tag_input_field').position().top + 30,
                left: $('#' + uniqueID + '_tag_input_field').position().left + 0
            });

            if (event.keyCode === 40) {

                // select next <li> element
                if (chosen === "") {
                    chosen = 1;
                } else if ((chosen + 1) < $('#' + uniqueID + '_tagpicker li').length) {
                    chosen++;
                }
                $('#' + uniqueID + '_tagpicker li').removeClass('selected');
                $('#' + uniqueID + '_tagpicker li:eq(' + chosen + ')').addClass('selected');
                return false;

            } else if (event.keyCode === 38) {

                // select previous <li> element
                if (chosen === "") {
                    chosen = 1;
                } else if (chosen > 0) {
                    chosen--;
                }
                $('#' + uniqueID + '_tagpicker li').removeClass('selected');
                $('#' + uniqueID + '_tagpicker li:eq(' + chosen + ')').addClass('selected');
                return false;

            } else if (event.keyCode === 13 || event.keyCode === 9) {

                var href = $('#' + uniqueID + '_tagpicker .selected a').attr('href');
                // simulate click event when href is not undefined.
                if (href !== undefined) {
                    window.location.href = href;
                }

            } else {

                // save the search string to variable
                var str = $('#' + uniqueID + '_tag_input_field').val();

                // show tagpicker with the results
                $('#' + uniqueID + '_tagpicker').show();

                // load tags
                loadTag(str);
            }
        } else {

            // hide tagpicker
            $('#' + uniqueID + '_tagpicker').hide();
        }


    });


    $('#' + uniqueID + '_tag_input_field').focusout(function () {

        // set the plain text including tag guids to the original input or textarea element
        $(options.inputId).val($.fn.tagpicker.parseTagInput(uniqueID));
    });


    function loadTag(keyword) {

        // remove existings entries
        $('#' + uniqueID + '_tagpicker li').remove();

        // show loader while loading
        $('#' + uniqueID + '_tagpicker').html('<li><div class="loader"><div class="sk-spinner sk-spinner-three-bounce"><div class="sk-bounce1"></div><div class="sk-bounce2"></div><div class="sk-bounce3"></div></div></div></li>');

        // build data object
        var data = options['data'] || {};
        
        //This is the preferred way of adding the keyword
        if(options['searchUrl'].indexOf('-keywordPlaceholder-') < 0) {
            data['keyword'] = keyword;
        }
        
        console.log(data);

        jQuery.getJSON(options.searchUrl.replace('-keywordPlaceholder-', keyword), data, function (json) {

            // remove existings entries
            $('#' + uniqueID + '_tagpicker li').remove();

            // sort by disabled/enabled and contains keyword
            json.sort(function(a,b) {
                if(a.name.indexOf(keyword) >= 0 && b.name.indexOf(keyword) < 0) {
                    return -1;
                } else if(a.name.indexOf(keyword) < 0 && b.name.indexOf(keyword) >= 0) {
                    return 1;
                }
                return 0;
            });


            if (json.length > 0) {

                for (var i = 0; i < json.length; i++) {

                    var _takenStyle = "";
                    var _takenData = false;
                   
                    // set options to link, that this entry is already taken or not available
                    if ($('#' + uniqueID + '_' + json[i].id).length || $('#'+json[i].id).length || json[i].id === options.tagId) {
                        _takenStyle = "opacity: 0.4;";
                        _takenData = true;
                    }

                    // build <li> entry
                    var str = '<li id="tag_' + json[i].id + '"><a style="' + _takenStyle + '" data-taken="' + _takenData + '" tabindex="-1" href="javascript:$.fn.tagpicker.addTag(\'' + json[i].id + '\', \'' + json[i].name.replace(/&#039;/g, "\\'") + '\', \'' + uniqueID + '\');">' + json[i].name + '</a></li>';

                    // append the entry to the <ul> list
                    $('#' + uniqueID + '_tagpicker').append(str);


                }

                // check if the list is empty
                if ($('#' + uniqueID + '_tagpicker').children().length === 0) {
                    // hide tagpicker, if it is
                    $('#' + uniqueID + '_tagpicker').hide();
                }

                // reset the variable for arrows keys
                chosen = "";

            } else {

                // hide tagpicker, if no tag was found
                $('#' + uniqueID + '_tagpicker').hide();
            }


            // remove hightlight
            $('#' + uniqueID + '_tagpicker li').removeHighlight();

            // add new highlight matching strings
            $('#' + uniqueID + '_tagpicker li').highlight(keyword);

            // add selection to the first space entry
            $('#' + uniqueID + '_tagpicker li:eq(0)').addClass('selected');

        });
    }

    function showHintTag() {

        // remove hint, if exists
        $('#maxTagsHint').remove();

        // build html structure
        var _html = '<div id="maxTagsHint" style="display: none;" class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">x</button><strong>Sorry!</strong> You can add a maximum of ' + options.maxTags + ' tags as admin for this group.</div>';

        // add hint to DOM
        $('#' + uniqueID + '_invite_tags').after(_html);

        // fadein hint
        $('#maxTagsHint').fadeIn('fast');
    };


};


// Add an tag for invitation
$.fn.tagpicker.addTag = function (guid, name, id) {
    
    if ($('#tag_' + guid + ' a').attr('data-taken') !== "true") {
      
        // Building a new <li> entry
        var _tagcode = '<li class="tagInput" id="' + id + '_' + guid + '">&nbsp;' + name + '<i class="fa fa-times-circle"></i></li>';


        // insert the new created <li> entry into the <ul> construct
        $('#' + id + '_tag_input').before(_tagcode);

        // remove tag, by clicking the close icon
        $('#' + id + '_' + guid + " i").click(function () {

            // remove tag tag
            $('#' + id + '_' + guid).remove();

            // reduce the count of added tag
            tagCount--;

        });

        // hide tag results
        $('#' + id + '_tagpicker').hide();

        // set focus to the input element
        $('#' + id + '_tag_input_field').focus();

        // Clear the textinput
        $('#' + id + '_tag_input_field').val('');

    }


};

$.fn.tagpicker.parseTagInput = function (id) {

    // create and insert a dummy <div> element to work with
    $('#' + id + '_invite_tags').after('<div id="' + id + '_inputResult"></div>');

    // set html form input element to the new <div> element
    $('#' + id + '_inputResult').html($('#' + id + '_invite_tags').html());


    $('#' + id + '_inputResult .tagInput').each(function () {

        // get tag guid without unique tagpicker id
        var pureID = this.id.replace(id + '_', '');
        
        // add the tag guid as plain text
        $(this).after(pureID + ",");

        // remove the link
        $(this).remove();
    });

    // save the plain text
    var result = $('#' + id + '_inputResult').text();

    // remove the dummy <div> element
    $('#' + id + '_inputResult').remove();

    // return the plain text
    return result;

};

