FONTS_TTF = \
	DejaVuSerif.ttf \
	DejaVuSerif-Bold.ttf \
	DejaVuSerif-BoldItalic.ttf \
	DejaVuSerif-Italic.ttf \
	DejaVuSans.ttf \
	DejaVuSans-Bold.ttf \
	DejaVuSans-BoldOblique.ttf \
	DejaVuSans-Oblique.ttf

FONTFORGE = fontforge

FONTS_OTF = $(FONTS_TTF:.ttf=.otf)
FONTS_EOT = $(FONTS_TTF:.ttf=.eot)
FONTS_AFM = $(FONTS_TTF:.ttf=.afm)
FONTS_WOFF = $(FONTS_TTF:.ttf=.woff)

all: otf eot afm woff

otf: $(FONTS_OTF)
eot: $(FONTS_EOT)
afm: $(FONTS_AFM)
woff: $(FONTS_WOFF)

%.otf: %.ttf
%.eot: %.ttf
%.afm: %.ttf
%.woff: %.ttf

%.otf %.eot %.afm %.woff: %.ttf
	$(FONTFORGE) -lang=ff -c $$'Open($$1)\nGenerate($$2)' $< $@

clean:
	$(RM) \
		$(FONTS_OTF) \
		$(FONTS_EOT) \
		$(FONTS_AFM) \
		$(FONTS_WOFF)

.PHONY: all clean \
	otf eot afm woff
