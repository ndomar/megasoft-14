import os
import Image



def main():
	# open an image file (.bmp,.jpg,.png,.gif) you have in the working folder
	baseDir = "new/"
	newDir = "images/"
	li = os.listdir("/Users/mohamed/workspace/entangle/megasoft-14/CompanyProfiles/Mohamed Shaban/new")
	for ele in li:
		imageFile = ele
		if imageFile == '.DS_Store':
			continue
		print imageFile
		im1 = Image.open(baseDir+imageFile)
		width  = 1440
		height = 900
		print "{},{}".format(width, height)
		im2 = im1.resize((width, height), Image.ANTIALIAS)    # best down-sizing filter
		im2.save(newDir+imageFile)

if __name__ == '__main__':
	main()