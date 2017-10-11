version = 0_0_00
outfile = PrestaShop_nl2go_$(version).zip

$(outfile):
	zip -r  build.zip ./newsletter2go/*
	mv build.zip $(outfile)

clean:
	rm -rf $(outfile)
