import os
import Image



def main():
	# open an image file (.bmp,.jpg,.png,.gif) you have in the working folder
	baseDir = "images/"
	newDir = "new/"
	li = os.listdir("/Users/mohamed/workspace/entangle/megasoft-14/CompanyProfiles/Mohamed Shaban/images")
	for ele in li:
		imageFile = ele
		if imageFile == '.DS_Store':
			continue
		print imageFile
		im1 = Image.open(baseDir+imageFile)
		width  = 1920
		height = 1200
		print "{},{}".format(width, height)
		im2 = im1.resize((width, height), Image.ANTIALIAS)    # best down-sizing filter
		im2.save(newDir+imageFile)

if __name__ == '__main__':
	main()