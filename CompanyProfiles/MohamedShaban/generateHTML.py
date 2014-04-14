import os


def main():
	li = os.listdir("/Users/mohamed/workspace/entangle/megasoft-14/CompanyProfiles/Mohamed Shaban/wall")
	i = 0;
	for ele in li:
		print "<img src=\"wall/{}\"  class = \"bg\" alt=\"\" id = \"bg{}\">".format(ele, i)
		i = i+1;

if __name__ == '__main__':
	main()
